<?php

namespace Epaphrodites\database\datas\arrays;

class datas
{

    /**
     * users Group list
     * 
     * @param int|string|null $key
     * @param string $need
     * @return array|int|string|null
     */
    public function usersGroup(
        int|string|null $key = null,
        string $need = '_id'
    ): array|int|string|null
    {

        $list = [
            1 => [ '_id' => 1, 'label' => 'ADMINISTRATEUR SYSTEME' ],
            2 => [ '_id' => 2, 'label' => 'ADMINISTRATION COMMUNALE' ],
            3 => [ '_id' => 3, 'label' => 'AGENT COMMUNAL' ],
            4 => [ '_id' => 4, 'label' => 'COMMERCANT' ],
        ];

        return $this->returnData($list, $key, $need );
    }

    /**
     * Bot instructions
     * 
     * @param string $lang
     * @param string $botName
     * @return string
     */
    public function botInstructions(
        string $lang, 
        string $botName
    ): string{
        $sections = [
            'fr' => [
                'presentation' => [
                    "# Instructions pour $botName",
                    "",
                    "## À propos",
                    "Tu es $botName, un assistant.",
                    "Tu es ivoirien",
                    "Tu reside en côte d'ivoire",
                    "Tu dois toujours être serviable, précis et concis dans tes réponses.",
                    "",
                    "## À propos d'Epaphrodites",
                    "Epaphrodites est un framework multi-langage (PHP, Python, C) conçu pour :",
                    "- La performance : optimisé pour des applications rapides et efficaces",
                    "- Interoperabilite : utilise plusieurs types de base de donnees (oracle, postgreSQL, Sql Serveur, Mysql, sqLite, MongoDB, redis ) au sein d'un meme projet",
                    "- La modularité : architecture flexible permettant d'ajouter/supprimer des composants",
                    "- La simplicité : courbe d'apprentissage rapide pour les développeurs",
                    "",
                    "Ce framework est idéal pour développer des applications web modernes et évolutives."
                ],
                'installation' => [
                    "## Procédure d'installation",
                    "",
                    "1. Créer un nouveau projet :",
                    "   ```bash",
                    "   composer create-project epaphrodites/epaphrodites nom_de_votre_projet",
                    "   ```",
                    "",
                    "2. Se positionner dans le répertoire du projet :",
                    "   ```bash",
                    "   cd nom_de_votre_projet",
                    "   ```",
                    "",
                    "3. Mettre à jour les dépendances :",
                    "   ```bash",
                    "   composer update",
                    "   ```",
                    "",
                    "4. Régénérer l'autoloader :",
                    "   ```bash",
                    "   composer dump-autoload",
                    "   ```",
                    "",
                    "5. Lancer le serveur de développement local :",
                    "   ```bash",
                    "   php heredia run:server --port=8000",
                    "   ```",
                    "",
                    "Une fois ces étapes terminées, l'application sera accessible à l'adresse http://localhost:8000"
                ]
            ],
            'en' => [
                'presentation' => [
                    "# Instructions for $botName",
                    "",
                    "## About",
                    "You are $botName, an assistant.",
                    "You are Ivorian.",
                    "You live in Côte d'Ivoire.",
                    "You must always be helpful, accurate, and concise in your answers.",
                    "",
                    "## About Epaphrodites",
                    "Epaphrodites is a multi-language framework (PHP, Python, C) designed for:",
                    "- Performance: optimized for fast and efficient applications",
                    "- Interoperability: supports multiple database types (Oracle, PostgreSQL, SQL Server, MySQL, SQLite, MongoDB, Redis) in the same project",
                    "- Modularity: flexible architecture allowing components to be added or removed",
                    "- Simplicity: quick learning curve for developers",
                    "",
                    "This framework is ideal for developing modern and scalable web applications."
                ],
                'installation' => [
                    "## Installation Procedure",
                    "",
                    "1. Create a new project:",
                    "   ```bash",
                    "   composer create-project epaphrodites/epaphrodites your_project_name",
                    "   ```",
                    "",
                    "2. Navigate to the project directory:",
                    "   ```bash",
                    "   cd your_project_name",
                    "   ```",
                    "",
                    "3. Update dependencies:",
                    "   ```bash",
                    "   composer update",
                    "   ```",
                    "",
                    "4. Regenerate the autoloader:",
                    "   ```bash",
                    "   composer dump-autoload",
                    "   ```",
                    "",
                    "5. Start the local development server:",
                    "   ```bash",
                    "   php heredia run:server --port=8000",
                    "   ```",
                    "",
                    "Once these steps are completed, the application will be accessible at http://localhost:8000"
                ]
            ]
        ];

        $content = [];
        foreach ($sections[$lang] as $section) {
            $content[] = implode("\n", $section);
        }

        return implode("\n\n", $content);
    }    

    /**
     * Get the list of dashboard colors or a specific color property by key.
     *
     * @param string|int|null $key The index of the color to retrieve (null to return the full list).
     * @param string $property The property to return ('_id' or 'label', defaults to '_id').
     * @return array{_id: string, label: string[]|string|null}
     */
    public function colorsList(
        string|int|null $key = null,
        string $need = '_id'
    ): array|string|null {

        $list = [
            ['_id' => 'main', 'label' => 'Main Colors'],
            ['_id' => 'noella', 'label' => 'Noella Colors'],
            ['_id' => 'shlomo', 'label' => 'Shlomo Colors'],
            ['_id' => 'yedidia', 'label' => 'Yedidiah Colors'],
            ['_id' => 'eklou', 'label' => 'Eklou Colors'],
        ];

        return $this->returnData($list, $key, $need );
    }
      
    /**
     * Authorization actions
     *
     * @return array
     */
    public function autorisation(): array {

        return [
            1 => 'DENY',
            2 => 'ALLOW',
        ];
    }

    /**
     * Validation actions for users
     * 
     * @return array
     */
    public function ActionsUsers():array
    {

       return
            [
                1 => "ENABLE / DISABLE AN ACCOUNT",
                2 => "RESET PASSWORD",
                3 => "UPDATE GROUP",
            ];
    }   
    
    /**
     * Rights actions
     * 
     * @return array
     */
    public function ActionsRights():array
    {
       return
            [
                1 => "GRANT PERMISSION",
                2 => "DENY PERMISSION",
                3 => "DELETE RIGHT",
            ];
    }  
    
    /**
     * Set users colors
     * 
     * @return array
     */
    public function colorsActions():array
    {
       return
            [
                1 => "SET USERS GROUP COLOR"
            ];
    }  
    
    /**
     * Summary of returnData
     * 
     * @param array $list
     * @param int|string|null $key
     * @param string $need
     * @return array|int|string|null
     */
    private function returnData( 
        array $list = [],
        int|string|null $key = null,
        string $need = '_id'
    ): array|int|string|null{

        if ($key === null) {
            return array_values($list);
        }

        if (!isset($list[$key])) {
            return null;
        }

        return $list[$key][$need] ?? null;
    }
}
