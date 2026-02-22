<?php

namespace Epaphrodites\controllers\controllers;

use Epaphrodites\controllers\switchers\MainSwitchers;

final class users extends MainSwitchers
{

    private object $msg;
    private object $session;
    private object $getId;    
    private object $count;
    private object $update;
    private object $select;
    private object $insert;
    private object $delete;
    private string $ans = '';
    private string $alert = '';
    private array|bool $result = [];
    private object $importFiles;

    /**
     * Human-friendly message when SQL TaxeConnect backend is not active.
     */
    private function sqlOnlyFeatureMessage(): void
    {
        $this->ans = "TaxeConnect account operations require an SQL driver (sqlite or mysql). Set _FIRST_DRIVER_ in bin/config/SetDirectory.php and run migrations.";
        $this->alert = 'alert-danger';
    }

    /**
     * Initialize object properties when an instance is created
     * @return  object
     */
    public final function __construct()
    {
        $this->initializeObjects();
    }

    /**
     * Initialize each property using values retrieved from static configurations
     * 
     * @return void
     */
    private function initializeObjects(): void
    {
        $this->msg = $this->getFunctionObject(static::initNamespace(), 'msg');
        $this->getId = $this->getFunctionObject(static::initQuery(), 'getid');
        $this->count = $this->getFunctionObject(static::initQuery(), 'count');
        $this->select = $this->getFunctionObject(static::initQuery(), 'select');
        $this->insert = $this->getFunctionObject(static::initQuery(), 'insert');
        $this->delete = $this->getFunctionObject(static::initQuery(), 'delete');
        $this->update = $this->getFunctionObject(static::initQuery(), 'update');
        $this->session = $this->getFunctionObject(static::initNamespace(), 'session');
        $this->importFiles = $this->getFunctionObject(static::initNamespace(), 'import');
    }         

    /**
     * Update user datas
     * 
     * @param string $html
     * @return void
     */
    public final function editUsersInfos(
        string $html
    ): void
    {

        $login = $this->session->login();

        if (static::isValidMethod(true)) {

            $this->result = $this->update->updateUserDatas(static::getPost('__username__'), static::getPost('__email__'), static::getPost('__contact__'), static::getPost('__otpStatus__'));
            
            [$this->ans, $this->alert] = static::Responses(
                $this->result, [  
                    true => ['succes', 'alert-success'], 
                    false => ['error', 'alert-danger'] 
                ]);
        }

        $this->views( $html, 
            [
                'alert' => $this->alert,
                'reponse' => $this->ans,
                'select' => $this->getId->GetUsersDatas($login),
            ],
            true
        );
    }

    /**
     * Update user password
     * 
     * @param string $html
     * @return void
     */
    public final function changePassword(
        string $html
    ): void
    {

        if (static::isValidMethod(true)) {

            $this->result = $this->update->changeUsersPassword(static::getPost('__oldpassword__'), static::getPost('__newpassword__'), static::getPost('__confirm__'));

            [$this->ans, $this->alert] = static::Responses(
                $this->result, [  
                    1 => ['no-identic', 'alert-danger'], 
                    2 => ['no-identic', 'alert-danger'],
                    3 => ['mdpnotsame', 'alert-danger']
                ]);
        }

        $this->views( $html, 
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
            ],
            true
        );
    }

    /**
     * Import users data
     * 
     * @param string $html
     * @return void
     */
    public final function importUsers(
        string $html
    ): void
    {
        if (!$this->allowRoles([1, 2])) {
            return;
        }

        if (static::isValidMethod(true)&&static::isFileName('__file__')) {
            
            $SheetData = $this->importFiles->importExcelFiles(static::getFileName('__file__'));

            if (!empty($SheetData)) {

                if(static::checkKeys(['users'],$SheetData)){

                    for ($i = 1; $i < count($SheetData); $i++) {

                        $usersLogin = $SheetData[$i][0];

                        $this->result = $this->insert->addUsers($usersLogin, static::getPost('__group__'));

                        [$this->ans, $this->alert] = static::Responses(
                            $this->result, [  
                                true => ['succes', 'alert-success'], 
                                false => ['error', 'alert-danger'] 
                            ]);
                    }

                }else{
                    $this->ans = $this->msg->answers('file-header');
                    $this->alert = 'alert-danger';
                }

            } else {
                $this->ans = $this->msg->answers('fileempty');
                $this->alert = 'alert-danger';
            }
        }

        $this->views( $html, 
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
            ],
            true
        );
    }

    /**
     * Show users list 
     * 
     * @param string $html
     * @return void
     */
    public final function allUsersList(
        string $html
    ): void
    {
        if (!$this->allowRoles([1, 2])) {
            return;
        }
        
        $total = 0;
        $list = [];
        $numLine = 100;
        $authoriz = false;
        $currentRole = $this->currentUserType();
        $isMairieScope = ($currentRole === 2);
        $currentLogin = (string) $this->session->login();
        $scopedLogins = $isMairieScope ? $this->select->mairieScopedLogins($currentLogin) : [];
        $userGroupsOptions = static::initNamespace()['datas']->usersGroup();
        if ($isMairieScope) {
            $userGroupsOptions = array_values(array_filter(
                $userGroupsOptions,
                static fn(array $group): bool => in_array((int) ($group['_id'] ?? 0), [3, 4], true)
            ));
        }
        $currentPage = static::isGet('_p', 'int') ? static::getGet('_p') : 1;
        
        $position = static::notEmpty(['filtre'] , 'GET') 
                        ? static::getGet('filtre') 
                        : NULL;

        if (static::isValidMethod(true)&&static::arrayNoEmpty(['users'])) {

            // Update users state
            if (static::isSelected('_sendselected_', 1 )) {

                foreach (static::isArray('users') as $login) {
                    if ($isMairieScope && !in_array((string) $login, $scopedLogins, true)) {
                        continue;
                    }

                    $this->result = $this->update->updateEtatsUsers($login);
                }

                [$this->ans, $this->alert] = static::Responses(
                    $this->result, [  
                        true => ['succes', 'alert-success'], 
                        false => ['error', 'alert-danger'] 
                    ]);                
            }

            // Init passwords
            if (static::isSelected('_sendselected_', 2 )) {

                foreach (static::isArray('users') as $login) {
                    if ($isMairieScope && !in_array((string) $login, $scopedLogins, true)) {
                        continue;
                    }

                    $this->result = $this->update->initUsersPassword($login);
                }

                [$this->ans, $this->alert] = static::Responses(
                    $this->result, [  
                        true => ['succes', 'alert-success'], 
                        false => ['error', 'alert-danger'] 
                    ]);                
            }

            // Update users group
            if (static::isSelected('_sendselected_', 3 )) {

                foreach (static::isArray('users') as $login) {
                    if ($isMairieScope && !in_array((string) $login, $scopedLogins, true)) {
                        continue;
                    }

                    $usersGroupSelected = static::getPost("{$login}__group__", true);

                    $this->result = $this->update->updateUsersGroup($login, $usersGroupSelected);
                }

                [$this->ans, $this->alert] = static::Responses(
                    $this->result, [  
                        true => ['succes', 'alert-success'], 
                        false => ['error', 'alert-danger'] 
                    ]);                
            }            
        }

        if ($isMairieScope) {
            $search = static::isGet('search') && static::notEmpty(['search'], 'GET')
                ? (string) static::getGet('search')
                : null;
            $groupFilter = static::notEmpty(['filtre'], 'GET')
                ? static::getGet('filtre')
                : null;
            if (!in_array((int) $groupFilter, [3, 4], true)) {
                $groupFilter = null;
            }

            $total = $this->select->countMairieScopedUsers($currentLogin, $groupFilter, $search);
            $list = $this->select->listMairieScopedUsers($currentLogin, $currentPage, $numLine, $groupFilter, $search);
            $authoriz = false;
        } else {
            if (static::isGet('search') && static::notEmpty(['search'] , 'GET')) {

                $list = $this->getId->GetUsersDatas(static::getGet('search'));
                $total = count($list ?? []);
                
            }else {

                $total = static::notEmpty(['filtre'] , 'GET') ? 
                                $this->count->CountUsersByGroup(static::getGet('filtre')) 
                                : $this->count->CountAllUsers();

                $list = static::notEmpty(['filtre'] , 'GET') 
                                ? $this->getId->GetUsersByGroup($currentPage, $numLine, static::getGet('filtre')) 
                                : $this->select->listeOfAllUsers($currentPage, $numLine);

                $authoriz = static::notEmpty(['filtre'], 'GET') ?? true;
            }
        }
        
        $this->views( $html, 
            [
                'total' => $total,
                'liste_users' => $list,
                'authoriz' => $authoriz,
                'alert' => $this->alert,
                'reponse' => $this->ans,
                'position' => $position,
                'select' => $this->getId,
                'user_groups_options' => $userGroupsOptions,
                'current' => $currentPage,
                'nbrePage' => ceil(($total) / $numLine),
            ],
            true
        );
    }

    /**
     * Super admin creates mairie account and commune binding.
     *
     * @param string $html
     * @return void
     */
    public final function createMairieAccount(
        string $html
    ): void
    {
        if (!$this->allowRoles([1])) {
            return;
        }

        if (static::isValidMethod(true)) {
            if (!in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
                $this->sqlOnlyFeatureMessage();
            } else {
            $payload = [
                'commune_code' => static::getPost('__commune_code__'),
                'commune_name' => static::getPost('__commune_name__'),
                'login' => static::getPost('__login__'),
                'password' => static::getPost('__password__'),
                'full_name' => static::getPost('__full_name__'),
                'email' => static::getPost('__email__'),
                'phone' => static::getPost('__phone__'),
            ];

            $this->result = $this->insert->createMairieAccount(
                $payload,
                (string) $this->session->login()
            );

                [$this->ans, $this->alert] = static::Responses(
                    $this->result,
                    [
                        true => ['succes', 'alert-success'],
                        false => ['error', 'alert-danger']
                    ]
                );

                if ($this->result === false) {
                    $this->ans = "Creation failed. Check that commune is unique, login is unique, and SQL tables (communes, users, mairies, usersaccount) are present.";
                    $this->alert = 'alert-danger';
                }
            }
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
            ],
            true
        );
    }

    /**
     * Mairie admin creates agent account in own commune.
     *
     * @param string $html
     * @return void
     */
    public final function createAgentAccount(
        string $html
    ): void
    {
        if (!$this->allowRoles([2])) {
            return;
        }

        if (static::isValidMethod(true)) {
            if (!in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
                $this->sqlOnlyFeatureMessage();
            } else {
            $payload = [
                'login' => static::getPost('__login__'),
                'password' => static::getPost('__password__'),
                'full_name' => static::getPost('__full_name__'),
                'email' => static::getPost('__email__'),
                'phone' => static::getPost('__phone__'),
            ];

            $this->result = $this->insert->createAgentAccount(
                $payload,
                (string) $this->session->login()
            );

                [$this->ans, $this->alert] = static::Responses(
                    $this->result,
                    [
                        true => ['succes', 'alert-success'],
                        false => ['error', 'alert-danger']
                    ]
                );

                if ($this->result === false) {
                    $this->ans = "Creation failed. Check that login is unique, your mairie account is linked to a commune, and SQL tables (users, agents, usersaccount) are present.";
                    $this->alert = 'alert-danger';
                }
            }
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
            ],
            true
        );
    }

    /**
     * Mairie admin creates taxes for own commune (catalog for agent assignment).
     *
     * @param string $html
     * @return void
     */
    public final function createCommuneTax(
        string $html
    ): void
    {
        if (!$this->allowRoles([2])) {
            return;
        }

        $taxes = [];
        $mairieLogin = (string) $this->session->login();

        if (static::isValidMethod(true)) {
            if (!in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
                $this->sqlOnlyFeatureMessage();
            } else {
                $payload = [
                    'code' => static::getPost('__code__'),
                    'label' => static::getPost('__label__'),
                    'amount' => static::getPost('__amount__'),
                    'frequency' => is_array(static::getPost('__frequency__', true))
                        ? (static::getPost('__frequency__', true)[0] ?? 'ANNUELLE')
                        : static::getPost('__frequency__'),
                    'status' => is_array(static::getPost('__status__', true))
                        ? (static::getPost('__status__', true)[0] ?? 'ACTIVE')
                        : static::getPost('__status__'),
                ];

                $this->result = $this->insert->createCommuneTax(
                    $payload,
                    $mairieLogin
                );

                [$this->ans, $this->alert] = static::Responses(
                    $this->result,
                    [
                        true => ['succes', 'alert-success'],
                        false => ['error', 'alert-danger']
                    ]
                );

                if ($this->result === false) {
                    $this->ans = "Creation taxe echouee. Verifie code unique par commune, libelle et montant.";
                }
            }
        }

        if (in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
            $taxes = $this->select->listCommuneTaxesByLogin($mairieLogin);
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
                'taxes' => $taxes,
            ],
            true
        );
    }

    /**
     * Agent creates merchant account in own commune.
     *
     * @param string $html
     * @return void
     */
    public final function createMerchantAccount(
        string $html
    ): void
    {
        if (!$this->allowRoles([3])) {
            return;
        }

        if (static::isValidMethod(true)) {
            if (!in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
                $this->sqlOnlyFeatureMessage();
            } else {
                $payload = [
                    'login' => static::getPost('__login__'),
                    'password' => static::getPost('__password__'),
                    'business_name' => static::getPost('__business_name__'),
                    'owner_name' => static::getPost('__owner_name__'),
                    'phone' => static::getPost('__phone__'),
                    'email' => static::getPost('__email__'),
                    'activity' => static::getPost('__activity__'),
                    'address' => static::getPost('__address__'),
                ];

                $this->result = $this->insert->createMerchantAccount(
                    $payload,
                    (string) $this->session->login()
                );

                [$this->ans, $this->alert] = static::Responses(
                    $this->result,
                    [
                        true => ['succes', 'alert-success'],
                        false => ['error', 'alert-danger']
                    ]
                );

                if ($this->result === false) {
                    $this->ans = "Creation compte commercant echouee. Verifie login unique et informations obligatoires.";
                }
            }
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
            ],
            true
        );
    }

    /**
     * Agent assigns one mairie tax type to a selected merchant.
     *
     * @param string $html
     * @return void
     */
    public final function assignTaxToMerchant(
        string $html
    ): void
    {
        if (!$this->allowRoles([3])) {
            return;
        }

        $taxes = [];
        $merchants = [];
        $assignments = [];
        $agentLogin = (string) $this->session->login();

        if (static::isValidMethod(true)) {
            if (!in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
                $this->sqlOnlyFeatureMessage();
            } else {
                $payload = [
                    'merchant_ref' => static::getPost('__merchant_ref__'),
                    'tax_id' => static::getPost('__tax_id__'),
                ];

                $this->result = $this->insert->assignTaxToMerchant(
                    $payload,
                    $agentLogin
                );

                [$this->ans, $this->alert] = static::Responses(
                    $this->result,
                    [
                        true => ['succes', 'alert-success'],
                        false => ['error', 'alert-danger']
                    ]
                );

                if ($this->result === false) {
                    $this->ans = "Attribution echouee. Verifie commercant, taxe active et doublon.";
                }
            }
        }

        if (in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
            $taxes = $this->select->listCommuneTaxesForAgent($agentLogin);
            $merchants = $this->select->listCommuneMerchantsForAgent($agentLogin);
            $assignments = $this->select->listTaxAssignmentsForAgent($agentLogin, 100);
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
                'taxes' => $taxes,
                'merchants' => $merchants,
                'assignments' => $assignments,
            ],
            true
        );
    }

    /**
     * Mairie admin deletes an agent account only.
     * Merchants and tax data created by this agent are preserved.
     *
     * @param string $html
     * @return void
     */
    public final function deleteAgentAccount(
        string $html
    ): void
    {
        if (!$this->allowRoles([2])) {
            return;
        }

        $agents = [];
        $mairieLogin = (string) $this->session->login();

        if (static::isValidMethod(true)) {
            if (!in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
                $this->sqlOnlyFeatureMessage();
            } else {
                $agentLogin = trim((string) static::getPost('__agent_login__'));
                $confirm = trim((string) static::getPost('__confirm_word__'));

                if ($agentLogin === '') {
                    $this->ans = "Selectionne un compte agent.";
                    $this->alert = 'alert-danger';
                } elseif ($confirm !== 'SUPPRIMER') {
                    $this->ans = "Confirmation invalide. Tape SUPPRIMER pour confirmer.";
                    $this->alert = 'alert-danger';
                } else {
                    $this->result = $this->delete->deleteAgentAccountOnly($mairieLogin, $agentLogin);

                    [$this->ans, $this->alert] = static::Responses(
                        $this->result,
                        [
                            true => ['succes', 'alert-success'],
                            false => ['error', 'alert-danger']
                        ]
                    );

                    if ($this->result === true) {
                        $this->ans = "Compte agent supprime: {$agentLogin}. Les commercants et donnees fiscales sont conserves.";
                    } else {
                        $this->ans = "Suppression echouee. Verifie que l'agent appartient a ta commune.";
                    }
                }
            }
        }

        if (in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
            $agents = $this->delete->listMairieAgents($mairieLogin);
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
                'agents' => $agents,
            ],
            true
        );
    }

    /**
     * Super admin deletes one commune account with cascade.
     *
     * @param string $html
     * @return void
     */
    public final function deleteCommuneAccount(
        string $html
    ): void
    {
        if (!$this->allowRoles([1])) {
            return;
        }

        $communeName = '';
        $scope = [];

        if (static::isValidMethod(true)) {
            if (!in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
                $this->sqlOnlyFeatureMessage();
            } else {
                $communeName = trim((string) static::getPost('__commune_name__'));

                if (static::isPost('__search__')) {
                    $scope = $this->delete->findCommuneAccountForDeletion($communeName);

                    if (empty($scope)) {
                        $this->ans = "Aucune commune trouvee avec ce nom.";
                        $this->alert = 'alert-warning';
                    }
                }

                if (static::isPost('__delete__')) {
                    $confirm = trim((string) static::getPost('__confirm_word__'));
                    if ($confirm !== 'SUPPRIMER') {
                        $this->ans = "Confirmation invalide. Tape SUPPRIMER pour confirmer.";
                        $this->alert = 'alert-danger';
                        $scope = $this->delete->findCommuneAccountForDeletion($communeName);
                    } else {
                        $this->result = $this->delete->deleteCommuneAccountCascade($communeName);

                        if ($this->result === true) {
                            $this->ans = "Suppression cascade terminee pour la commune: {$communeName}.";
                            $this->alert = 'alert-success';
                            $scope = [];
                            $communeName = '';
                        } else {
                            $this->ans = "Suppression echouee. Verifie le nom de la commune et les tables SQL.";
                            $this->alert = 'alert-danger';
                            $scope = $this->delete->findCommuneAccountForDeletion($communeName);
                        }
                    }
                }
            }
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
                'commune_name' => $communeName,
                'scope' => $scope,
            ],
            true
        );
    }

    /**
     * Super admin suspends/reactivates one mairie account by commune.
     *
     * @param string $html
     * @return void
     */
    public final function suspendMairieAccount(
        string $html
    ): void
    {
        if (!$this->allowRoles([1])) {
            return;
        }

        $communeName = '';
        $scope = [];

        if (static::isValidMethod(true)) {
            if (!in_array(_FIRST_DRIVER_, ['sqlite', 'mysql'], true)) {
                $this->sqlOnlyFeatureMessage();
            } else {
                $communeName = trim((string) static::getPost('__commune_name__'));

                if (static::isPost('__search__')) {
                    $scope = $this->delete->findCommuneAccountForDeletion($communeName);
                    if (empty($scope)) {
                        $this->ans = "Aucune commune trouvee avec ce nom.";
                        $this->alert = 'alert-warning';
                    }
                }

                if (static::isPost('__suspend__')) {
                    $confirm = trim((string) static::getPost('__confirm_word__'));
                    if ($confirm !== 'SUSPENDRE') {
                        $this->ans = "Confirmation invalide. Tape SUSPENDRE pour confirmer.";
                        $this->alert = 'alert-danger';
                    } else {
                        $result = $this->update->suspendMairieAccountByCommune($communeName);
                        if ($result === true) {
                            $this->ans = "Compte mairie suspendu pour la commune: {$communeName}.";
                            $this->alert = 'alert-success';
                        } else {
                            $this->ans = "Suspension echouee. Verifie le nom de la commune.";
                            $this->alert = 'alert-danger';
                        }
                    }
                    $scope = $this->delete->findCommuneAccountForDeletion($communeName);
                }

                if (static::isPost('__reactivate__')) {
                    $confirm = trim((string) static::getPost('__confirm_word__'));
                    if ($confirm !== 'ACTIVER') {
                        $this->ans = "Confirmation invalide. Tape ACTIVER pour confirmer.";
                        $this->alert = 'alert-danger';
                    } else {
                        $result = $this->update->reactivateMairieAccountByCommune($communeName);
                        if ($result === true) {
                            $this->ans = "Compte mairie reactive pour la commune: {$communeName}.";
                            $this->alert = 'alert-success';
                        } else {
                            $this->ans = "Reactivation echouee. Verifie le nom de la commune.";
                            $this->alert = 'alert-danger';
                        }
                    }
                    $scope = $this->delete->findCommuneAccountForDeletion($communeName);
                }
            }
        }

        $this->views(
            $html,
            [
                'reponse' => $this->ans,
                'alert' => $this->alert,
                'commune_name' => $communeName,
                'scope' => $scope,
            ],
            true
        );
    }
}
