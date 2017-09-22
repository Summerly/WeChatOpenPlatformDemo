<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $echostr = $request->get('echostr');

        if ($echostr) {
            if ($this->checkSignature($request)) {
                return new Response($echostr);
            }
        } else {
            return $this->responseMsg();
        }
    }

    private function checkSignature(Request $request)
    {
        $signature = $request->get('signature');
        $timestamp = $request->get('timestamp');
        $nonce = $request->get('nonce');

        $token = $this->getParameter('token');
        $tmpArr = [
            $token, $timestamp, $nonce
        ];
        sort($tmpArr);
        $temStr = implode($tmpArr);
        $tmpStr = sha1($temStr);

        if ($tmpStr == $signature) {
            return true;
        }

        return false;
    }

    private function responseMsg()
    {
        $postStr = file_get_contents("php://input");

        if (!empty($postStr)) {
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            $fromUsername = $postObj->FromUserName;
            $toUserName = $postObj->ToUserName;
            $keyword = trim($postObj->Content);
            $time = time();
            $textTpl = "<xml>
                            <ToUserName><![CDATA[%s]]></ToUserName>
                            <FromUserName><![CDATA[%s]]></FromUserName>
                            <CreateTime>%s</CreateTime>
                            <MsgType><![CDATA[%s]]></MsgType>
                            <Content><![CDATA[%s]]></Content>
                            <MsgId>0</MsgId>
                        </xml>
";
            if ($keyword) {
                $msgType = "text";
                $content = date("Y-m-d H:i:s", time());
                $result = sprintf($textTpl, $fromUsername, $toUserName, $time, $msgType, $content);

                return new Response($result);
            }
        } else {
            return new Response('');
        }
    }
}
