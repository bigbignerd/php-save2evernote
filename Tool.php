<?php

class Tool
{
	/**
	 * 正则匹配xhtml中的笔记内容
	 * @author bignerd
	 * @since  2017-01-09T16:15:56+0800
	 * @param  [type] $content
	 */
	public static function handleEvernoteContent($content)
	{
		$pattern = '/<en-note>(.*)<\/en-note>/is';

    	preg_match_all($pattern, $content, $matches);

    	return $matches[1][0];
	}
	/**
	 * 预处理保存信息
	 * @author bignerd
	 * @since  2017-01-09T16:15:07+0800
	 * @param  $content
	 */
	public static function handleSaveContent($content)
	{
		return '- '.$content.' --'.date('m/d H:i',time());
	}
	/**
	 * 获取现在属于本月的第几周
	 * @author bignerd
	 * @since  2017-01-09T15:22:35+0800
	 */
	public static function weekIndexOfMonth()
	{
		$day  = date('j');//几号
		$index = ceil($day / 7);
		$NO = ['','一','二','三','四'];
		return $NO[$index];
	}

}
?>