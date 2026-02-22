<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\Extension\Fonts;

use Twig\TwigFilter;
use Twig\TwigFunction;
use Epaphrodites\epaphrodites\Extension\Functions\SetTwigFunctions;

class TwigExtension extends SetTwigFunctions
{
    public function getFunctions():array
    {
        return 
        [
            new TwigFunction('__csrf', [ $this , 'csrf_token_twig']),
            new TwigFunction('__xCsrf', [ $this , 'X_Crsf_Token_twig']),
            new TwigFunction('__truncate', [ $this ,  'truncatePath_twig']),
            new TwigFunction('__replace', [ $this , 'replace_funct']),
            new TwigFunction('__path', [ $this , 'mainPath_twig']),
            new TwigFunction('__pathId', [ $this , 'mainidPath_twig']),
            new TwigFunction('__host', [ $this , 'hostPath_twig']),
            new TwigFunction('__prev', [ $this , 'previousPath_twig']),
            new TwigFunction('__logout', [ $this , 'logoutPath_twig']),
            new TwigFunction('__login', [ $this , 'login_twig']),
            new TwigFunction('__usertype', [ $this , 'typeusers_twig']),
            new TwigFunction('__nbreformat', [ $this , 'ifformat_twig']),
            new TwigFunction('__msg', [ $this , 'msgPath_twig']),
            new TwigFunction('__QRCodes' , [ $this , 'QRcodes_twig']),
            new TwigFunction('__GoogleQRCodes' , [ $this , 'GoogleQRCodes_twig']),
            new TwigFunction('__explode', [ $this , 'datasExplode']),
            new TwigFunction('__img', [ $this , 'imagePath_twig']),
            new TwigFunction('__css', [ $this , 'cssPath_twig']),
            new TwigFunction('__cssfont', [ $this , 'cssfontPath_twig']),
            new TwigFunction('__cssiconfont', [ $this , 'cssiconfontPath_twig']),
            new TwigFunction('__cssbsico', [ $this , 'cssbsicontPath_twig']),
            new TwigFunction('__js', [ $this , 'jsPath_twig']),
            new TwigFunction('__docs', [ $this , 'pdfPath_twig']),        
        ]; 
    }

    public function getFilters(): array
    {
        return
        [
            new TwigFilter('strpad', [ $this , 'twig_strptad']),
            new TwigFilter('toiso', [ $this , 'isoPath_twig']),
            new TwigFilter('fullDate', [ $this , 'LongPath_twig']),
            new TwigFilter('dates', [ $this , 'dates_twig']),
            new TwigFilter('truncate', [ $this , 'truncatePath_twig'])
        ];
    }     

}