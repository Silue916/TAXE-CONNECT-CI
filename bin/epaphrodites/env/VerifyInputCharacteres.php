<?php

namespace Epaphrodites\epaphrodites\env;

class VerifyInputCharacteres extends MatchCharactere
{

  /** 
  * Verify if is only character and number
  */
  public function onlyNumberAndCharacter($InputCharacteres, $nbre)
  {

    return $this->WithoutNumberAndCharacters($InputCharacteres) === false && $this->CountCharacterNumber($InputCharacteres) < $nbre ? false : true;
  }

  /**
  * Verify if is only number
  */
  public function onlyNumber($InputCharacteres, $nbre)
  {

    return $this->WithoutNumber($InputCharacteres) === false && $this->CountCharacterNumber($InputCharacteres) < $nbre ? false : true;
  }

  /** 
  * Verify if is only character
  */
  public function onlyCharacter($InputCharacteres, $nbre)
  {

    return $this->WithoutCharacters($InputCharacteres) === false && $this->CountCharacterNumber($InputCharacteres) < $nbre ? false : true;
  }
}
