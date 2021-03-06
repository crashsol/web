<?php
require_once('_visitorcommon.php');
require_once('/php/sql/sqlweixin.php');

function _echoWeixinVisitorData($sql, $strUser, $iStart, $iNum, $bChinese)
{
    $arId = array();
    
	$key_sql = $sql->GetKeySql();
	$log_sql = $sql->GetLogSql();
    if ($result = $sql->GetAll($iStart, $iNum)) 
    {
        while ($record = mysql_fetch_assoc($result)) 
        {
            $strContent = $log_sql->GetKey($record['weixintext_id']);
            $strContent = GetVisitorContentsDisplay($strContent);
            
            if ($strUser)     $strLink = GetVisitorSrcDisplay($strUser);
            else
            {
                $strId = $record['weixin_id'];
				$str = $key_sql->GetKey($strId);
                $strDisplay = GetVisitorSrcDisplay($str);
                if (in_array($strId, $arId))    $strLink = $strDisplay;
                else
                {
                    $strLink = GetPhpLink('/account/weixinvisitor', 'id='.$str, $strDisplay, false, $bChinese);
                    $arId[] = $strId;
                }
            }
            
            EchoVisitorItem($strContent, $strLink, $record);
        }
        @mysql_free_result($result);
    }
}

function _echoWeixinVisitorParagraph($strUser, $iStart, $iNum, $bChinese)
{
    if ($bChinese)     
    {
        $arColumn = array('参数', 'OpenID', '日期', '时间');
    }
    else
    {
        $arColumn = array('Parameter', 'OpenID', 'Date', 'Time');
    }
    
   	$sql = new WeixinVisitorSql($strUser);
    $strNavLink = GetNavLink(($strUser ? 'id='.$strUser : false), $sql->Count(), $iStart, $iNum, $bChinese);

    EchoVisitorParagraphBegin($arColumn, $strNavLink, $strUser, $bChinese);
    _echoWeixinVisitorData($sql, $strUser, $iStart, $iNum, $bChinese);
    EchoTableParagraphEnd($strNavLink);
}

function EchoWeixinVisitor($bChinese = true)
{
    $strUser = UrlGetQueryValue('id');
    if ($strUser)
    {
        $str = $strUser;
    }
    else
    {
//        $str = GetVisitorTodayLink(SqlCountTableToday(TABLE_WEIXIN_VISITOR), $bChinese);
        $str = '';
    }
    EchoParagraph($str);
    
    $iStart = UrlGetQueryInt('start');
    $iNum = UrlGetQueryInt('num', DEFAULT_NAV_DISPLAY);
    _echoWeixinVisitorParagraph($strUser, $iStart, $iNum, $bChinese);
    EchoVisitorCommonLinks($bChinese);
}

    AcctAuth();

?>
