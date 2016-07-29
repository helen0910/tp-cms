<?php
// require LIBRARY_PATH.'ORG/Mail/phpmailer.php';
// require 'phpmailer.php';
require LIBRARY_PATH.'ORG/Net/Mail/class.phpmailer.php';
class Mail {
	/**
	 * 
	 * @param string $address 发送的Email
	 * @param string $subject  主题
	 * @param string $content  内容
	 * @param Array $Attachment 附件
	 * @return boolean
	 */
	public static function sendMail($address,$subject,$content,$Attachment = array()) {
		$Mail = new PHPMailer();
		$Mail->IsSMTP();
// 		$Mail->SMTPDebug  = 2;//Mail发送调试  0 = off (for production use),1 = client messages,2 = client and server messages
// 		$Mail->Debugoutput = 'html';//Html调试输出
		$Mail->Host       = C('EMAIL_HOST');
		$Mail->Port       = C('EMAIL_PORT');
		$Mail->SMTPAuth   = true;
		$Mail->Username   = C('EMAIL_USER');
		$Mail->Password   = C('EMAIL_PASS');
		$Mail->SetFrom(C('EMAIL_USER'),C('WEB_NAME'));
		
// 		$Mail->AddReplyTo('594737142@qq.com','发件人名');//用户回复的email收件人， 不填写默认发件人

		$Mail->AddAddress($address);//发送的Email
		$Mail->Subject = $subject;//主题
		// 			从外部文件阅读HTML邮件正文，参考图像转换为嵌入式，HTML转换成一个基本纯文本替代体
		$Mail->MsgHTML($content);//Html内容
		
		// 		更换有一个手动创建的纯文本正文（暂时没发现什么用）
		// 		$Mail->AltBody = '这是一个主体说明Body';
		// 		添加附件
		// 		$Mail->AddAttachment('images/phpmailer_mini.gif');
		$sendResult = $Mail->Send();
		if(!$sendResult) {
			$Model = new GlobalModel();
			$Model->writeLog("E-mail发送失败，错误信息{$Mail->ErrorInfo}，发件人：$address，内容：$content", 'SYSTEM_ERROR');
		}
		return $sendResult;
	} 
}
?>