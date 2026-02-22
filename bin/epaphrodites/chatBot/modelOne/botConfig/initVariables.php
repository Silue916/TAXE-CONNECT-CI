<?php

namespace Epaphrodites\epaphrodites\chatBot\modelOne\botConfig;

trait initVariables
{

  // Initialize variables to store the best coefficient and the response
  public string $botKey = 'key';
  public string $nameKey = 'name';
  public string $dateKey = 'date';
  public string $loginKey = 'login';
  public string $contextKey = 'context';
  public string $answersKey = 'answers';
  public string $actionsKey = 'actions';
  public string $defaultLanguage = 'eng';
  public string $previousKey = 'previous';
  public string $assemblyKey = 'assembly';
  public string $questionKey = 'question';
  public string $languageKey = 'language';
  public string $similarlyKey = 'similarly';
  public string $coefficientKey = 'coefficient';
  public string $auxiliary_verbs = 'auxiliary_verbs';
  public string $neutral_words = 'neutral_words';
  public string $negative_words = 'negative_words';
  public string $negations = 'negations';
  public string $attenuators = 'attenuators';
  public string $intensifiers = 'intensifiers';

  public array $userWords = [];
  public array $response = [];
  public string $bestAnswers = '';
  public int $coefficient = 0;
  public ?array $maxComment = null;
  public array $defaultUsers = [];
  public bool $previous = false;
  public string $makeAction = 'none';
  public array $defaultMessage = [];
  public int $bestCoefficient = 0;
  public string $similarySentence = "";
  public float $mainCoefficient = 0.3;
  public array $questionsAnswers = [];
  public array $temporaryResponses = [];
}