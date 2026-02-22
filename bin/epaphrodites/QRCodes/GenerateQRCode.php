<?php

namespace Epaphrodites\epaphrodites\QRCodes;

class GenerateQRCode extends GetQRcode{

    /**
     * @param mixed $datas
     * @return mixed
    */
    public function GenerateQRCodeDatas($datas){
        
        $QRCode = $this->Qrcode($datas);

        echo "<img src='$QRCode' alt='QRCode' class='qrcodes'>";
    }

    /**
     * @param mixed $datas
     * @return mixed
    */    
    public function GenerateGoogleQRCode($datas){

        $QRCode = $this->GoogleQRCode($datas);

        echo "<img src='$QRCode' alt='Google QRCode' class='qrcodes'>";
    }

    /**
     * @param mixed $datas
     * @return mixed
    */        
    public function PrintQRCode($datas){

        $QRCode = $this->FpdfQRCode($datas);

        echo "<img src='$QRCode' alt='FPDF QRCode' class='qrcodes'>";
    }
}