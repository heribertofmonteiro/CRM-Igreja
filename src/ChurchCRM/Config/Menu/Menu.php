<?php

namespace ChurchCRM\Config\Menu;

use ChurchCRM\Authentication\AuthenticationManager;
use ChurchCRM\Config;
use ChurchCRM\dto\SystemConfig;
use ChurchCRM\model\ChurchCRM\GroupQuery;
use ChurchCRM\model\ChurchCRM\ListOptionQuery;
use ChurchCRM\model\ChurchCRM\MenuLinkQuery;

class MenuImprovedImproved
{
    /**
     * @var array<string, MenuItem>|null
     */
    private static ?array $menuItems = null;

    public static function init(): void
    {
        self::$menuItems = self::buildImprovedMenuItems();
    }

    public static function getMenu(): ?array
    {
        return self::$menuItems;
    }

    private static function buildImprovedMenuItems(): array
    {
        return [
            // 🏠 Dashboard Principal
            'Dashboard' => new MenuItem(gettext('Dashboard'), 'v2/dashboard', true, 'fa-tachometer-alt'),
            
            // 👥 Gestão de Pessoas (Agrupado)
            'People' => self::getPeopleMenuImproved(),
            
            // 📅 Agenda e Eventos (Agrupado)
            'Calendar' => self::getCalendarMenuImproved(),
            
            // 🎯 Ministérios e Grupos (Agrupado)
            'Ministry' => self::getMinistryMenuImproved(),
            
            // 🏫 Escola Dominical (Agrupado)
            'Education' => self::getEducationMenuImproved(),
            
            // 💰 Finanças (Agrupado)
            'Finance' => self::getFinanceMenuImproved(),
            
            // 📧 Comunicação (Agrupado)
            'Communication' => self::getCommunicationMenuImproved(),
            
            // 📊 Relatórios (Agrupado)
            'Reports' => self::getReportsMenuImproved(),
            
            // ⚙️ Administração (Agrupado)
            'Admin' => self::getAdminMenuImproved(),
            
            // 🔗 Links Personalizados
            'Custom' => self::getCustomMenu(),
        ];
    }

    private static function getPeopleMenuImproved(): MenuItem
    {
        $peopleMenu = new MenuItem(gettext('People'), '', true, 'fa-user-group');
        
        // 📋 Cadastro e Gestão
        $cadastroMenu = new MenuItem(gettext('📋 Registration'), '', true, 'fa-user-plus');
        $cadastroMenu->addSubMenu(new MenuItem(gettext('Add New Person'), 'PersonEditor.php', AuthenticationManager::getCurrentUser()->isAddRecordsEnabled()));
        $cadastroMenu->addSubMenu(new MenuItem(gettext('Add New Family'), 'FamilyEditor.php', AuthenticationManager::getCurrentUser()->isAddRecordsEnabled()));
        $peopleMenu->addSubMenu($cadastroMenu);
        
        // 👥 Visualização
        $viewMenu = new MenuItem(gettext('👥 View'), '', true, 'fa-eye');
        $viewMenu->addSubMenu(new MenuItem(gettext('Active People'), 'v2/people'));
        $viewMenu->addSubMenu(new MenuItem(gettext('Inactive People'), 'v2/people?familyActiveStatus=inactive'));
        $viewMenu->addSubMenu(new MenuItem(gettext('All People'), 'v2/people?familyActiveStatus=all'));
        $viewMenu->addSubMenu(new MenuItem(gettext('Active Families'), 'v2/family'));
        $viewMenu->addSubMenu(new MenuItem(gettext('Inactive Families'), 'v2/family?mode=inactive'));
        $peopleMenu->addSubMenu($viewMenu);
        
        // 🏠 Dashboard
        $peopleMenu->addSubMenu(new MenuItem(gettext('🏠 People Dashboard'), 'PeopleDashboard.php'));
        
        // ⚙️ Administração de Pessoas
        if (AuthenticationManager::getCurrentUser()->isAdmin()) {
            $adminPeopleMenu = new MenuItem(gettext('⚙️ People Admin'), '', true, 'fa-cog');
            $adminPeopleMenu->addSubMenu(new MenuItem(gettext('Classifications'), 'OptionManager.php?mode=classes'));
            $adminPeopleMenu->addSubMenu(new MenuItem(gettext('Family Roles'), 'OptionManager.php?mode=famroles'));
            $adminPeopleMenu->addSubMenu(new MenuItem(gettext('Family Properties'), 'PropertyList.php?Type=f'));
            $adminPeopleMenu->addSubMenu(new MenuItem(gettext('Family Custom Fields'), 'FamilyCustomFieldsEditor.php'));
            $adminPeopleMenu->addSubMenu(new MenuItem(gettext('People Properties'), 'PropertyList.php?Type=p'));
            $adminPeopleMenu->addSubMenu(new MenuItem(gettext('Person Custom Fields'), 'PersonCustomFieldsEditor.php'));
            $adminPeopleMenu->addSubMenu(new MenuItem(gettext('Volunteer Opportunities'), 'VolunteerOpportunityEditor.php'));
            $peopleMenu->addSubMenu($adminPeopleMenu);
        }

        return $peopleMenu;
    }

    private static function getCalendarMenuImproved(): MenuItem
    {
        $calendarMenu = new MenuItem(gettext('Calendar & Events'), '', true, 'fa-calendar-alt');
        
        // 📅 Calendário Principal
        $calendarMenu->addSubMenu(new MenuItem(gettext('📅 Calendar'), 'v2/calendar', SystemConfig::getBooleanValue('bEnabledCalendar')));
        
        // 🎉 Eventos
        $eventsMenu = new MenuItem(gettext('🎉 Events'), '', SystemConfig::getBooleanValue('bEnabledEvents'), 'fa-ticket-alt');
        $eventsMenu->addSubMenu(new MenuItem(gettext('Add Event'), 'EventEditor.php', AuthenticationManager::getCurrentUser()->isAddEventEnabled()));
        $eventsMenu->addSubMenu(new MenuItem(gettext('List Events'), 'ListEvents.php'));
        $eventsMenu->addSubMenu(new MenuItem(gettext('Event Types'), 'EventNames.php', AuthenticationManager::getCurrentUser()->isAddEventEnabled()));
        $eventsMenu->addSubMenu(new MenuItem(gettext('Check-in/Check-out'), 'Checkin.php'));
        $eventsMenu->addSubMenu(new MenuItem(gettext('Attendance Reports'), 'EventAttendance.php'));
        $calendarMenu->addSubMenu($eventsMenu);
        
        // 📊 Contadores
        $calendarMenu->addCounter(new MenuCounter('BirthdateNumber', 'bg-primary', 0, gettext("Today's Birthdays")));
        $calendarMenu->addCounter(new MenuCounter('AnniversaryNumber', 'bg-dark', 0, gettext("Today's Wedding Anniversaries")));
        $calendarMenu->addCounter(new MenuCounter('EventsNumber', 'bg-warning', 0, gettext('Events Today')));

        return $calendarMenu;
    }

    private static function getMinistryMenuImproved(): MenuItem
    {
        $ministryMenu = new MenuItem(gettext('Ministry & Groups'), '', true, 'fa-hands-helping');
        
        // 🎯 Ministérios
        $hasPermission = AuthenticationManager::getCurrentUser()->isAdmin() || AuthenticationManager::getCurrentUser()->isEditRecordsEnabled();
        $ministryMenu->addSubMenu(new MenuItem(gettext('🎯 Ministries'), 'v2/ministerio', $hasPermission));
        $ministryMenu->addSubMenu(new MenuItem(gettext('🤝 Groups'), 'GroupList.php'));
        
        // 📋 Gestão de Ministérios
        if ($hasPermission) {
            $ministrySubMenu = new MenuItem(gettext('📋 Ministry Management'), '', true, 'fa-tasks');
            $ministrySubMenu->addSubMenu(new MenuItem(gettext('Ministry Dashboard'), 'v2/ministerio'));
            $ministrySubMenu->addSubMenu(new MenuItem(gettext('Meetings'), 'v2/ministerio/reunioes'));
            $ministrySubMenu->addSubMenu(new MenuItem(gettext('Messages'), 'v2/ministerio/mensagens'));
            $ministryMenu->addSubMenu($ministrySubMenu);
        }
        
        // 👥 Grupos Dinâmicos
        $listOptions = ListOptionQuery::create()->filterById(3)->orderByOptionSequence()->find();
        foreach ($listOptions as $listOption) {
            if ($listOption->getOptionId() !== 4) {
                $tmpMenu = self::addGroupSubMenus($listOption->getOptionName(), $listOption->getOptionId(), 'GroupView.php?GroupID=');
                if ($tmpMenu instanceof MenuItem) {
                    $ministryMenu->addSubMenu($tmpMenu);
                }
            }
        }
        
        // 🏠 Administração de Grupos
        if (AuthenticationManager::getCurrentUser()->isAdmin()) {
            $adminGroupMenu = new MenuItem(gettext('⚙️ Groups Admin'), '', true, 'fa-cog');
            $adminGroupMenu->addSubMenu(new MenuItem(gettext('Group Properties'), 'PropertyList.php?Type=g'));
            $adminGroupMenu->addSubMenu(new MenuItem(gettext('Group Types'), 'OptionManager.php?mode=grptypes'));
            $ministryMenu->addSubMenu($adminGroupMenu);
        }

        return $ministryMenu;
    }

    private static function getEducationMenuImproved(): MenuItem
    {
        $educationMenu = new MenuItem(gettext('Education'), '', SystemConfig::getBooleanValue('bEnabledSundaySchool'), 'fa-graduation-cap');
        
        // 🏫 Escola Dominical
        $educationMenu->addSubMenu(new MenuItem(gettext('🏫 Sunday School'), 'sundayschool/SundaySchoolDashboard.php'));
        
        // 📚 Classes
        $tmpMenu = self::addGroupSubMenus(gettext('📚 Classes'), 4, 'sundayschool/SundaySchoolClassView.php?groupId=');
        if ($tmpMenu instanceof MenuItem) {
            $educationMenu->addSubMenu($tmpMenu);
        }

        return $educationMenu;
    }

    private static function getFinanceMenuImproved(): MenuItem
    {
        $hasFinancePermission = SystemConfig::getBooleanValue('bEnabledFinance') && AuthenticationManager::getCurrentUser()->isFinanceEnabled();
        $hasFundraiserPermission = SystemConfig::getBooleanValue('bEnabledFundraiser');
        
        $financeMenu = new MenuItem(gettext('Finance'), '', ($hasFinancePermission || $hasFundraiserPermission), 'fa-money-bill-wave');
        
        if ($hasFinancePermission) {
            // 💰 Depósitos
            $depositsMenu = new MenuItem(gettext('💰 Deposits'), '', true, 'fa-cash-register');
            $depositsMenu->addSubMenu(new MenuItem(gettext('View All Deposits'), 'FindDepositSlip.php'));
            $depositsMenu->addSubMenu(new MenuItem(gettext('Deposit Reports'), 'FinancialReports.php'));
            if (isset($_SESSION['iCurrentDeposit']) && $_SESSION['iCurrentDeposit'] > 0) {
                $depositsMenu->addSubMenu(new MenuItem(gettext('Edit Current Deposit'), 'DepositSlipEditor.php?DepositSlipID=' . $_SESSION['iCurrentDeposit']));
            }
            $depositsMenu->addCounter(new MenuCounter('iCurrentDeposit', 'bg-green', $_SESSION['iCurrentDeposit'] ?? 0));
            $financeMenu->addSubMenu($depositsMenu);
            
            // 📊 Relatórios Financeiros
            $reportsMenu = new MenuItem(gettext('📊 Financial Reports'), '', true, 'fa-chart-bar');
            $reportsMenu->addSubMenu(new MenuItem(gettext('Financial Reports'), 'FinancialReports.php'));
            $reportsMenu->addSubMenu(new MenuItem(gettext('Tax Report'), 'TaxReport.php'));
            $financeMenu->addSubMenu($reportsMenu);
        }
        
        if ($hasFundraiserPermission) {
            // 🎁 Fundraisers
            $fundraiserMenu = new MenuItem(gettext('🎁 Fundraisers'), '', true, 'fa-money-bill-alt');
            $fundraiserMenu->addSubMenu(new MenuItem(gettext('Create New Fundraiser'), 'FundRaiserEditor.php?FundRaiserID=-1'));
            $fundraiserMenu->addSubMenu(new MenuItem(gettext('View All Fundraisers'), 'FindFundRaiser.php'));
            if (isset($_SESSION['iCurrentFundraiser']) && $_SESSION['iCurrentFundraiser'] > 0) {
                $fundraiserMenu->addSubMenu(new MenuItem(gettext('Edit Current Fundraiser'), 'FundRaiserEditor.php?FundRaiserID=' . $_SESSION['iCurrentFundraiser']));
            }
            $fundraiserMenu->addSubMenu(new MenuItem(gettext('Add Donors to Buyer List'), 'AddDonors.php'));
            $fundraiserMenu->addSubMenu(new MenuItem(gettext('View Buyers'), 'PaddleNumList.php'));
            $iCurrentFundraiser = $_SESSION['iCurrentFundraiser'] ?? 0;
            $fundraiserMenu->addCounter(new MenuCounter('iCurrentFundraiser', 'bg-blue', $iCurrentFundraiser));
            $financeMenu->addSubMenu($fundraiserMenu);
        }

        return $financeMenu;
    }

    private static function getCommunicationMenuImproved(): MenuItem
    {
        $communicationMenu = new MenuItem(gettext('Communication'), '', SystemConfig::getBooleanValue('bEnabledEmail'), 'fa-envelope');
        
        // 📧 Email
        $communicationMenu->addSubMenu(new MenuItem(gettext('📧 Email Dashboard'), 'v2/email/dashboard'));
        
        // 📱 Comunicação
        $commSubMenu = new MenuItem(gettext('📱 Communication'), '', true, 'fa-comments');
        $commSubMenu->addSubMenu(new MenuItem(gettext('Send Email'), 'v2/email/dashboard'));
        $commSubMenu->addSubMenu(new MenuItem(gettext('SMS Messages'), 'v2/sms'));
        $commSubMenu->addSubMenu(new MenuItem(gettext('Notifications'), 'v2/notifications'));
        $communicationMenu->addSubMenu($commSubMenu);

        return $communicationMenu;
    }

    private static function getReportsMenuImproved(): MenuItem
    {
        $reportsMenu = new MenuItem(gettext('Reports & Analytics'), '', true, 'fa-chart-line');
        
        // 📊 Relatórios Principais
        $reportsMenu->addSubMenu(new MenuItem(gettext('📊 Query Menu'), 'QueryList.php'));
        
        // 📈 Analytics
        $analyticsMenu = new MenuItem(gettext('📈 Analytics'), '', true, 'fa-chart-pie');
        $analyticsMenu->addSubMenu(new MenuItem(gettext('People Analytics'), 'v2/analytics/people'));
        $analyticsMenu->addSubMenu(new MenuItem(gettext('Family Analytics'), 'v2/analytics/family'));
        $analyticsMenu->addSubMenu(new MenuItem(gettext('Attendance Analytics'), 'v2/analytics/attendance'));
        $analyticsMenu->addSubMenu(new MenuItem(gettext('Financial Analytics'), 'v2/analytics/finance'));
        $reportsMenu->addSubMenu($analyticsMenu);
        
        // 📋 Relatórios Personalizados
        $reportsMenu->addSubMenu(new MenuItem(gettext('📋 Custom Reports'), 'v2/reports/custom'));

        return $reportsMenu;
    }

    private static function getAdminMenuImproved(): MenuItem
    {
        $adminMenu = new MenuItem(gettext('Administration'), '', AuthenticationManager::getCurrentUser()->isAdmin(), 'fa-tools');
        
        // ⚙️ Configurações do Sistema
        $configMenu = new MenuItem(gettext('⚙️ System Settings'), '', true, 'fa-cog');
        $configMenu->addSubMenu(new MenuItem(gettext('General Settings'), 'SystemSettings.php'));
        $configMenu->addSubMenu(new MenuItem(gettext('Property Types'), 'PropertyTypeList.php'));
        $adminMenu->addSubMenu($configMenu);
        
        // 👥 Gestão de Usuários
        $userMenu = new MenuItem(gettext('👥 User Management'), '', true, 'fa-users');
        $userMenu->addSubMenu(new MenuItem(gettext('System Users'), 'UserList.php'));
        $userMenu->addSubMenu(new MenuItem(gettext('User Roles'), 'v2/admin/roles'));
        $userMenu->addSubMenu(new MenuItem(gettext('User Permissions'), 'v2/admin/permissions'));
        $adminMenu->addSubMenu($userMenu);
        
        // 🗄️ Banco de Dados
        $dbMenu = new MenuItem(gettext('🗄️ Database'), '', true, 'fa-database');
        $dbMenu->addSubMenu(new MenuItem(gettext('Backup Database'), 'BackupDatabase.php'));
        $dbMenu->addSubMenu(new MenuItem(gettext('Restore Database'), 'RestoreDatabase.php'));
        $dbMenu->addSubMenu(new MenuItem(gettext('Reset System'), 'v2/admin/database/reset'));
        $adminMenu->addSubMenu($dbMenu);
        
        // 📤 Import/Export
        $importExportMenu = new MenuItem(gettext('📤 Import/Export'), '', true, 'fa-exchange-alt');
        $importExportMenu->addSubMenu(new MenuItem(gettext('CSV Import'), 'CSVImport.php'));
        $importExportMenu->addSubMenu(new MenuItem(gettext('CSV Export'), 'CSVExport.php', AuthenticationManager::getCurrentUser()->isCSVExport()));
        $adminMenu->addSubMenu($importExportMenu);
        
        // 🔧 Ferramentas
        $toolsMenu = new MenuItem(gettext('🔧 Tools'), '', true, 'fa-wrench');
        $toolsMenu->addSubMenu(new MenuItem(gettext('Kiosk Manager'), 'KioskManager.php'));
        $toolsMenu->addSubMenu(new MenuItem(gettext('Custom Menus'), 'v2/admin/menus'));
        $toolsMenu->addSubMenu(new MenuItem(gettext('Debug'), 'v2/admin/debug'));
        $toolsMenu->addSubMenu(new MenuItem(gettext('System Logs'), 'v2/admin/logs'));
        $adminMenu->addSubMenu($toolsMenu);

        return $adminMenu;
    }

    private static function getCustomMenu(): MenuItem
    {
        $menu = new MenuItem(gettext('Links'), '', SystemConfig::getBooleanValue('bEnabledMenuLinks'), 'fa-link');
        $menuLinks = MenuLinkQuery::create()->orderByOrder()->find();
        foreach ($menuLinks as $link) {
            $menu->addSubMenu(new MenuItem($link->getName(), $link->getUri()));
        }

        return $menu;
    }

    private static function addGroupSubMenus($menuName, $groupId, string $viewURl): ?MenuItem
    {
        $groups = GroupQuery::create()->filterByType($groupId)->orderByName()->find();
        if (!$groups->isEmpty()) {
            $unassignedGroups = new MenuItem($menuName, '', true, 'fa-tag');
            foreach ($groups as $group) {
                $unassignedGroups->addSubMenu(
                    new MenuItem(
                        $group->getName(),
                        $viewURl . $group->getID(),
                        true,
                        'fa-user-tag'
                    )
                );
            }

            return $unassignedGroups;
        }

        return null;
    }
}
