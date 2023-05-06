<?php
 
 namespace App\Service;

use App\Entity\Utilisateur;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\Label\Alignment\LabelAlignmentCenter;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\Result\ResultInterface;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Component\HttpFoundation\Request;


class QrCodeGenerator 
{
    public function createQrCode(Request $request): ResultInterface
    {
        $info = rand(10000, 99999);
        // Generate the QR code with user information
        $session = $request->getSession();
        $session->set('info',$info);
       
        $result = Builder::create()
        ->writer(new SvgWriter())
        ->writerOptions([])
        ->data($info)
        ->encoding(new Encoding('UTF-8'))
        ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
        ->size(200)
        ->margin(10)
        ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
        ->labelText('Vous trouvez vos informations ici')
        ->labelFont(new NotoSans(20))
        ->labelAlignment(new LabelAlignmentCenter())
        ->validateResult(false)
        ->build();
        return $result;
    }
}