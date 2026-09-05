
 

 
<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="{{ URL::to('') }}" class="logo logo-dark">
            <span class="logo-sm"><img src="{{ URL::asset('build/images/logo-sm.png') }}" height="22"></span>
            <span class="logo-lg"><img src="{{ URL::asset('build/images/logo-dark.png') }}" height="35"></span>
        </a>
        <!-- Light Logo-->
        <a href="{{ URL::to('') }}" class="logo logo-light">
            <span class="logo-sm"><img src="{{ URL::asset('build/images/logo-sm.png') }}" height="22"></span>
            <span class="logo-lg"><img src="{{ URL::asset('build/images/logo-light.png') }}" height="35"></span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">
            <div id="two-column-menu"></div>

            <ul class="navbar-nav" id="navbar-nav">

                <!-- MAIN NAVIGATION -->
                <li class="menu-title"><span>MAIN NAVIGATION</span></li>
                @if(in_array($currentRoleSlug, ['root','sadmin','admin','manager','operater']))
                <!-- Dashboard -->
                <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                    <a class="nav-link menu-link {{ request()->is('dashboard') ? 'active' : '' }}"
                       href="#menuDashboard" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->is('dashboard') ? 'true' : 'false' }}"
                       aria-controls="menuDashboard">
                       <i class="ri-dashboard-2-line me-2"></i> 
                       <span>Dashboard</span>
                    </a>

                    <div class="menu-dropdown collapse {{ request()->is('dashboard') ? 'show' : '' }}" id="menuDashboard">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item {{ request()->is('dashboard') ? 'active' : '' }}">
                                <a href="{{ url('dashboard') }}" class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}">
                                    <i class="ri-user-star-line me-2"></i> 
                                    Dashboard
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
               
            <li class="nav-item {{ request()->routeIs('leads.*') ? 'active' : '' }}">
    <a class="nav-link menu-link {{ request()->routeIs('leads.*') ? 'active' : '' }}"
       href="#menuLeads"
       data-bs-toggle="collapse"
       aria-expanded="{{ request()->routeIs('leads.*') ? 'true' : 'false' }}"
       aria-controls="menuLeads">
       <i class="ri-contacts-book-line me-2"></i>
       <span>Leads Manager</span>
    </a>

    <div class="menu-dropdown collapse {{ request()->routeIs('leads.*') ? 'show' : '' }}"
         id="menuLeads">

        <ul class="nav nav-sm flex-column">

            <!-- All Leads -->
            <li class="nav-item">
                <a href="{{ route('Leads.ListView') }}"
                   class="nav-link {{ request()->routeIs('Leads.ListView') ? 'active' : '' }}">
                    <i class="ri-list-check me-2"></i> 
                    All Leads
                </a>
            </li>

            <!-- Upload Logs -->
            <li class="nav-item">
				<a href="{{ route('leads.upload.logs') }}"
				   class="nav-link {{ request()->routeIs('leads.upload.logs') ? 'active' : '' }}">
					<i class="ri-upload-cloud-2-line me-2"></i>
					Upload Logs
				</a>
			</li>

        </ul>
    </div>
</li>
 

	@endif
				@if(in_array($currentRoleSlug, ['root','sadmin','admin']))
				<!-- AUTOMATION LEADS -->

			<li class="nav-item {{ request()->is('automation*') ? 'active' : '' }}">
    <a class="nav-link menu-link {{ request()->is('automation*') ? 'active' : '' }}"
       href="#menuRawData" data-bs-toggle="collapse"
       aria-expanded="{{ request()->is('automation*') ? 'true' : 'false' }}"
       aria-controls="menuRawData">
        <i class="ri-database-2-line me-2"></i>
        <span>Raw Data</span>
    </a>

    <div class="menu-dropdown collapse {{ request()->is('automation*') ? 'show' : '' }}"
         id="menuRawData">

        <ul class="nav nav-sm flex-column">

           	<li class="nav-item">
    <a href="{{ url('automation/raw-leads/uploads') }}"
       class="nav-link {{ request()->is('automation/raw-leads/uploads*') ? 'active' : '' }}">
       <i class="ri-upload-cloud-2-line me-2"></i> Upload Files
    </a>
</li> <li class="nav-item">
                <a href="{{ url('automation/raw-leads') }}"
                   class="nav-link {{ request()->is('automation/raw-leads*') ? 'active' : '' }}">
                   <i class="ri-stack-line me-2"></i> Raw Leads Panel
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('automation/ivr') }}"
                   class="nav-link {{ request()->is('automation/ivr*') ? 'active' : '' }}">
                   <i class="ri-phone-fill me-2"></i> IVR Call Logs
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('automation/whatsapp') }}"
                   class="nav-link {{ request()->is('automation/whatsapp*') ? 'active' : '' }}">
                   <i class="ri-whatsapp-fill me-2"></i> WhatsApp Logs
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('automation/sms') }}"
                   class="nav-link {{ request()->is('automation/sms*') ? 'active' : '' }}">
                   <i class="ri-message-fill me-2"></i> SMS Logs
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('automation/verified-leads') }}"
                   class="nav-link {{ request()->is('automation/verified-leads*') ? 'active' : '' }}">
                   <i class="ri-checkbox-circle-line me-2"></i> Verified Leads
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('automation/rejected-leads') }}"
                   class="nav-link {{ request()->is('automation/rejected-leads*') ? 'active' : '' }}">
                   <i class="ri-close-circle-line me-2"></i> Rejected Leads
                </a>
            </li>

        </ul>
    </div>
</li>


			<!-- FORM DESK -->
@php
$formDeskActive =
    request()->is('webforms*') ||
    request()->is('landing-pages*') ||
    request()->is('application_forms*');
@endphp

<li class="nav-item {{ $formDeskActive ? 'active' : '' }}">
    <a class="nav-link menu-link {{ $formDeskActive ? 'active' : '' }}"
       href="#menuFormDesk" data-bs-toggle="collapse"
       aria-expanded="{{ $formDeskActive ? 'true' : 'false' }}"
       aria-controls="menuFormDesk">
        <i class="ri-pages-line me-2"></i> 
        <span>FormDesk</span>
    </a>

    <div class="menu-dropdown collapse {{ $formDeskActive ? 'show' : '' }}"
         id="menuFormDesk">

        <ul class="nav nav-sm flex-column">

            <li class="nav-item {{ request()->is('webforms*') ? 'active' : '' }}">
                <a href="{{ url('webforms') }}"
                   class="nav-link {{ request()->is('webforms*') ? 'active' : '' }}">
                    <i class="ri-file-list-3-line me-2"></i> 
                    Manage Forms
                </a>
            </li>

            <li class="nav-item {{ request()->is('landing-pages*') ? 'active' : '' }}">
                <a href="{{ route('landing-pages.index') }}"
                   class="nav-link {{ request()->is('landing-pages*') ? 'active' : '' }}">
                    <i class="ri-file-list-3-line me-2"></i> 
                    Landing Pages 
                </a>
            </li>

            <li class="nav-item {{ request()->is('application_forms*') ? 'active' : '' }}">
                <a href="{{ route('application.forms') }}"
                   class="nav-link {{ request()->is('application_forms*') ? 'active' : '' }}">
                    <i class="ri-booklet-line me-2"></i> 
                    Application Forms
                </a>
            </li>

        </ul>
    </div>
</li>



      @endif
				@if(in_array($currentRoleSlug, ['root','sadmin','admin','manager']))	
					<!-- INSTITUTE & PROGRAM MANAGER -->
	@php
$instituteActive =
    request()->is('institute-manager*') ||
    request()->is('products*') ||
    request()->is('view-institute*') ||
    request()->is('view-program*');
@endphp

<li class="nav-item {{ $instituteActive ? 'active' : '' }}">

    <a class="nav-link menu-link {{ $instituteActive ? 'active' : '' }}"
       href="#menuInstitute" data-bs-toggle="collapse"
       aria-expanded="{{ $instituteActive ? 'true' : 'false' }}"
       aria-controls="menuInstitute">

        <i class="ri-school-line me-2"></i>
        <span>Institute Manager</span>
    </a>

    <div class="menu-dropdown collapse {{ $instituteActive ? 'show' : '' }}" id="menuInstitute">

        <ul class="nav nav-sm flex-column">

            <li class="nav-item {{ request()->is('institute-manager*') ? 'active' : '' }}">
                <a href="{{ url('institute-manager') }}"
                   class="nav-link {{ request()->is('institute-manager*') ? 'active' : '' }}">
                    <i class="ri-stack-line me-2"></i>
                    Institutes
                </a>
            </li>

            <li class="nav-item {{ request()->is('products*') ? 'active' : '' }}">
                <a href="{{ url('products') }}"
                   class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                    <i class="ri-book-open-line me-2"></i>
                    Courses
                </a>
            </li>

        </ul>
    </div>
</li>

                 @endif
				 
				 
				 
		@if(in_array($currentRoleSlug, ['root','sadmin','admin','manager']))
@php
$couponActive =
    request()->is('coupons') ||
    request()->is('coupons/create') ||
    request()->is('coupons/*/edit') ||
    request()->is('coupons/*/usage');
@endphp

<li class="nav-item {{ $couponActive ? 'active' : '' }}">

    <a class="nav-link menu-link {{ $couponActive ? 'active' : '' }}"
       href="#menuCoupons" data-bs-toggle="collapse"
       aria-expanded="{{ $couponActive ? 'true' : 'false' }}"
       aria-controls="menuCoupons">

        <i class="ri-coupon-3-line me-2"></i>
        <span>Marketing</span>
    </a>

    <div class="menu-dropdown collapse {{ $couponActive ? 'show' : '' }}" id="menuCoupons">

        <ul class="nav nav-sm flex-column">

            <!-- Coupon List -->
            <li class="nav-item {{ request()->is('coupons') ? 'active' : '' }}">
                <a href="{{ route('coupons.index') }}"
                   class="nav-link {{ request()->is('coupons') ? 'active' : '' }}">
                    <i class="ri-list-check-2 me-2"></i>
                    Coupon List
                </a>
            </li>

            <!-- Create Coupon -->
            <li class="nav-item {{ request()->is('coupons/create') ? 'active' : '' }}">
                <a href="{{ route('coupons.create') }}"
                   class="nav-link {{ request()->is('coupons/create') ? 'active' : '' }}">
                    <i class="ri-add-circle-line me-2"></i>
                    Create Coupon
                </a>
            </li>

        </ul>
    </div>
</li>


@endif
		 
				 
				 
                  @if(in_array($currentRoleSlug, ['root','sadmin','admin']))              <!-- USER ACCESS CONTROL -->
       @php
$uacActive =
    request()->is('user*') ||
    request()->is('role*') ||
    request()->is('teams*') ||
    request()->is('designations*') ||
    request()->is('organizations*');
@endphp

<li class="nav-item {{ $uacActive ? 'active' : '' }}">
    <a class="nav-link menu-link {{ $uacActive ? 'active' : '' }}"
        href="#menuUAC" data-bs-toggle="collapse"
        aria-expanded="{{ $uacActive ? 'true' : 'false' }}"
        aria-controls="menuUAC">
        <i class="ri-shield-user-fill me-2"></i> 
        <span>User Access Control</span>
    </a>

    <div class="menu-dropdown collapse {{ $uacActive ? 'show' : '' }}"
        id="menuUAC">
        <ul class="nav nav-sm flex-column">

            <li class="nav-item {{ request()->is('user*') ? 'active' : '' }}">
                <a href="{{ url('user') }}" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                    <i class="ri-user-line me-2"></i> Manage Users
                </a>
            </li>

            @if(in_array($currentRoleSlug, ['root','sadmin']))
            <li class="nav-item {{ request()->is('role*') ? 'active' : '' }}">
                <a href="{{ url('role') }}" class="nav-link {{ request()->is('role*') ? 'active' : '' }}">
                    <i class="ri-lock-line me-2"></i> Manage Permissions
                </a>
            </li>
            @endif

            <li class="nav-item {{ request()->is('teams*') ? 'active' : '' }}">
                <a href="{{ url('teams') }}" class="nav-link {{ request()->is('teams*') ? 'active' : '' }}">
                    <i class="ri-team-line me-2"></i> Teams
                </a>
            </li>

            <li class="nav-item {{ request()->is('designations*') ? 'active' : '' }}">
                <a href="{{ url('designations') }}" class="nav-link {{ request()->is('designations*') ? 'active' : '' }}">
                    <i class="ri-briefcase-line me-2"></i> Designations
                </a>
            </li>

            <li class="nav-item {{ request()->is('organizations*') ? 'active' : '' }}">
                <a href="{{ url('organizations') }}" class="nav-link {{ request()->is('organizations*') ? 'active' : '' }}">
                    <i class="ri-home-3-fill me-2"></i> Organizations
                </a>
            </li>

        </ul>
    </div>
</li>

                <!-- MASTER DATA -->
			@php
$masterDataActive =
    request()->is('pipelines*') ||
    request()->is('sources*') ||
    request()->is('types*') ||
    request()->is('attributes*') ||
    request()->is('webhooks*') ||
    request()->is('campaigns*') ||
    request()->is('marketing-events*');
@endphp

<li class="nav-item {{ $masterDataActive ? 'active' : '' }}">

    <a class="nav-link menu-link {{ $masterDataActive ? 'active' : '' }}"
       href="#menuMasterData" data-bs-toggle="collapse"
       aria-expanded="{{ $masterDataActive ? 'true' : 'false' }}">

        <i class="ri-database-2-line me-2"></i>
        <span>Master Data</span>
    </a>

    <div class="menu-dropdown collapse {{ $masterDataActive ? 'show' : '' }}"
         id="menuMasterData">

        <ul class="nav nav-sm flex-column">

            <li class="nav-item">
                <a href="{{ url('pipelines') }}"
                   class="nav-link {{ request()->is('pipelines*') ? 'active' : '' }}">
                    <i class="ri-ancient-gate-line me-2"></i> Pipelines
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('sources') }}"
                   class="nav-link {{ request()->is('sources*') ? 'active' : '' }}">
                    <i class="ri-links-line me-2"></i> Lead Sources
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('types') }}"
                   class="nav-link {{ request()->is('types*') ? 'active' : '' }}">
                    <i class="ri-price-tag-2-line me-2"></i> Lead Medium
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('attributes') }}"
                   class="nav-link {{ request()->is('attributes*') ? 'active' : '' }}">
                    <i class="ri-grid-line me-2"></i> Attributes
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('webhooks') }}"
                   class="nav-link {{ request()->is('webhooks*') ? 'active' : '' }}">
                    <i class="ri-git-pull-request-line me-2"></i> Webhooks
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('campaigns') }}"
                   class="nav-link {{ request()->is('campaigns*') ? 'active' : '' }}">
                    <i class="ri-megaphone-line me-2"></i> Campaigns
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ url('marketing-events') }}"
                   class="nav-link {{ request()->is('marketing-events*') ? 'active' : '' }}">
                    <i class="ri-calendar-event-line me-2"></i> Marketing Events
                </a>
            </li>

        </ul>
    </div>
</li>

                <!-- TEMPLATE MANAGER -->
 		@php
$templateActive =
    request()->is('email-templates*') ||
    request()->is('whatsapp-templates*') ||
    request()->is('message-templates*');
@endphp

<li class="nav-item {{ $templateActive ? 'active' : '' }}">
    <a class="nav-link menu-link {{ $templateActive ? 'active' : '' }}"
        href="#menuTemplates" data-bs-toggle="collapse"
        aria-expanded="{{ $templateActive ? 'true' : 'false' }}"
        aria-controls="menuTemplates">
        <i class="ri-folder-line me-2"></i>
        <span>Template Manager</span>
    </a>

    <div class="menu-dropdown collapse {{ $templateActive ? 'show' : '' }}"
         id="menuTemplates">

        <ul class="nav nav-sm flex-column">

            <li class="nav-item {{ request()->is('email-templates*') ? 'active' : '' }}">
                <a href="{{ route('email.templates.index') }}"
                   class="nav-link {{ request()->is('email-templates*') ? 'active' : '' }}">
                    <i class="ri-mail-send-line me-2"></i> Email Templates
                </a>
            </li>

            <li class="nav-item {{ request()->is('whatsapp-templates*') ? 'active' : '' }}">
                <a href="{{ url('whatsapp-templates') }}"
                   class="nav-link {{ request()->is('whatsapp-templates*') ? 'active' : '' }}">
                    <i class="ri-whatsapp-line me-2"></i> WhatsApp Templates
                </a>
            </li>

            <li class="nav-item {{ request()->is('message-templates*') ? 'active' : '' }}">
                <a href="{{ url('message-templates') }}"
                   class="nav-link {{ request()->is('message-templates*') ? 'active' : '' }}">
                    <i class="ri-chat-1-line me-2"></i> SMS Templates
                </a>
            </li>

        </ul>
    </div>
</li>

			
			
			
			
			
			
			
			
@php

$settingsOpen = request()->is(
    'general-settings*',
    'graphics-settings*',
    'email-settings*',
    'whatsapp-settings*',
    'sms-settings*',
    'ivr-settings*',
    'lead-settings*',
    'lead-distribution*',
    'lead-integrations*',
    'settings/automation*'
);

$configOpen = request()->is(
    'general-settings*',
    'graphics-settings*',
    'email-settings*',
    'whatsapp-settings*',
    'sms-settings*',
    'ivr-settings*'
);

$leadSettingsOpen = request()->is(
    'lead-settings*',
    'lead-distribution*'
);

$leadIntegrationOpen = request()->is('lead-integrations*');

$automationOpen = request()->is('settings/automation*');

@endphp

<li class="nav-item {{ $settingsOpen ? 'active' : '' }}">

    <!-- SETTINGS PARENT -->
    <a class="nav-link menu-link {{ $settingsOpen ? 'active' : '' }}"
       href="#menuSettings"
       data-bs-toggle="collapse"
       role="button"
       aria-expanded="{{ $settingsOpen ? 'true' : 'false' }}"
       aria-controls="menuSettings">

        <i class="ri-settings-3-line me-2"></i>
        <span>Settings</span>
    </a>

    <div class="collapse menu-dropdown {{ $settingsOpen ? 'show' : '' }}"
         id="menuSettings"
         data-bs-parent="#sidebar-menu">
					<ul class="nav nav-sm flex-column">

            <!-- ================= CONFIGURATION ================= -->
            <li class="nav-item">

                <a href="#menuConfig"
                   class="nav-link {{ $configOpen ? 'active' : '' }}"
                   data-bs-toggle="collapse"
                   aria-expanded="{{ $configOpen ? 'true' : 'false' }}">

                    <i class="ri-tools-line me-2"></i> Configuration
                </a>

                <div class="collapse menu-dropdown {{ $configOpen ? 'show' : '' }}" id="menuConfig">
                    <ul class="nav nav-sm flex-column">

                        <li>
                            <a href="{{ url('general-settings') }}"
                               class="nav-link {{ request()->is('general-settings*') ? 'active' : '' }}">
                                <i class="ri-settings-4-line me-2"></i> General Settings
                            </a>
                        </li>

                        <li>
                            <a href="javascript:void(0)"
                               data-bs-toggle="offcanvas"
                               data-bs-target="#theme-settings-offcanvas"
                               class="nav-link {{ request()->is('graphics-settings*') ? 'active' : '' }}">
                                <i class="ri-brush-line me-2"></i> Graphics Settings
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('email-settings') }}"
                               class="nav-link {{ request()->is('email-settings*') ? 'active' : '' }}">
                                <i class="ri-mail-settings-line me-2"></i> Email Settings
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('whatsapp-settings') }}"
                               class="nav-link {{ request()->is('whatsapp-settings*') ? 'active' : '' }}">
                                <i class="ri-whatsapp-line me-2"></i> WhatsApp Settings
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('sms-settings') }}"
                               class="nav-link {{ request()->is('sms-settings*') ? 'active' : '' }}">
                                <i class="ri-message-2-line me-2"></i> SMS Settings
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('ivr-settings') }}"
                               class="nav-link {{ request()->is('ivr-settings*') ? 'active' : '' }}">
                                <i class="ri-phone-settings-line me-2"></i> IVR Settings
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

            <!-- ================= LEAD SETTINGS ================= -->
            <li class="nav-item">

                <a href="#menuLeadSettings"
                   class="nav-link {{ $leadSettingsOpen ? 'active' : '' }}"
                   data-bs-toggle="collapse"
                   aria-expanded="{{ $leadSettingsOpen ? 'true' : 'false' }}">

                    <i class="ri-list-settings-line me-2"></i> Lead Settings
                </a>

                <div class="collapse menu-dropdown {{ $leadSettingsOpen ? 'show' : '' }}"
                     id="menuLeadSettings">

                    <ul class="nav nav-sm flex-column">
						<li class="nav-item">
							<a href="{{ route('lead-settings/settings') }}"
							   class="nav-link {{ request()->is('lead-settings/settings*') ? 'active' : '' }}">
								<i class="ri-settings-3-line me-2"></i> Lead Settings  
							</a>
						</li>

                        <li>
                            <a href="{{ url('lead-settings/statuses') }}"
                               class="nav-link {{ request()->is('lead-settings/statuses*') ? 'active' : '' }}">
                                <i class="ri-node-tree me-2"></i> Lead Status
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('lead-settings/sub-statuses') }}"
                               class="nav-link {{ request()->is('lead-settings/sub-statuses*') ? 'active' : '' }}">
                                <i class="ri-git-commit-line me-2"></i> Lead Sub Status
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('lead.distribution.index') }}"
                               class="nav-link {{ request()->is('lead-distribution*') ? 'active' : '' }}">
                                <i class="ri-git-branch-line me-2"></i> Distribution Rules
                            </a>
                        </li>

                    </ul>
                </div>
            </li>
<!-- ================= AUTOMATION SETTINGS ================= -->
<li class="nav-item">

    @php
        $automationOpen = request()->is('settings/automation*');
    @endphp

    <a href="#menuAutomationSettings"
       class="nav-link {{ $automationOpen ? 'active' : '' }}"
       data-bs-toggle="collapse"
       aria-expanded="{{ $automationOpen ? 'true' : 'false' }}">

        <i class="ri-flashlight-line me-2"></i> Automation Rules
    </a>

    <div class="collapse menu-dropdown {{ $automationOpen ? 'show' : '' }}"
         id="menuAutomationSettings">

        <ul class="nav nav-sm flex-column">

            <li>
                <a href="{{ route('automation.index') }}"
                   class="nav-link {{ request()->routeIs('automation.index') ? 'active' : '' }}">
                    <i class="ri-list-check-2 me-2"></i> All Rules
                </a>
            </li>

            <li>
                <a href="{{ route('automation.create') }}"
                   class="nav-link {{ request()->routeIs('automation.create') ? 'active' : '' }}">
                    <i class="ri-add-circle-line me-2"></i> Create Rule
                </a>
            </li>

        </ul>
    </div>
</li>

            <!-- ================= LEAD INTEGRATIONS ================= -->
            <li class="nav-item">

                <a href="#menuLeadIntegrations"
                   class="nav-link {{ $leadIntegrationOpen ? 'active' : '' }}"
                   data-bs-toggle="collapse"
                   aria-expanded="{{ $leadIntegrationOpen ? 'true' : 'false' }}">

                    <i class="ri-links-line me-2"></i> Lead Integrations
                </a>

                <div class="collapse menu-dropdown {{ $leadIntegrationOpen ? 'show' : '' }}"
                     id="menuLeadIntegrations">

                    <ul class="nav nav-sm flex-column">

                        <li>
                            <a href="{{ route('lead.integrations.facebook') }}"
                               class="nav-link {{ request()->is('lead-integrations/facebook*') ? 'active' : '' }}">
                                <i class="ri-facebook-circle-line me-2"></i> Facebook Lead Ads
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('lead.integrations.instagram') }}"
                               class="nav-link {{ request()->is('lead-integrations/instagram*') ? 'active' : '' }}">
                                <i class="ri-instagram-line me-2"></i> Instagram Lead Ads
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('lead.integrations.google') }}"
                               class="nav-link {{ request()->is('lead-integrations/google*') ? 'active' : '' }}">
                                <i class="ri-google-line me-2"></i> Google Lead Forms
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('lead.integrations.logs') }}"
                               class="nav-link {{ request()->is('lead-integrations/logs*') ? 'active' : '' }}">
                                <i class="ri-file-list-3-line me-2"></i> Webhook Logs
                            </a>
                        </li>

                    </ul>
                </div>
            </li>

        </ul>
    </div>
</li>


                @endif

             @if(in_array($currentRoleSlug, ['root','sadmin','admin','manager']))
    <!-- REPORTS -->
    <li class="nav-item">
        <a href="#menuReports" class="nav-link" data-bs-toggle="collapse"
           aria-expanded="false">
           <i class="ri-bar-chart-grouped-line me-2"></i>
           <span>Reports</span>
        </a>

        <div class="collapse menu-dropdown" id="menuReports">
            <ul class="nav nav-sm flex-column">

                {{-- Lead Distribution Report --}}
                <li class="nav-item">
                    <a href="{{ route('reports.lead.distribution') }}"
                       class="nav-link {{ request()->is('reports/lead-distribution') ? 'active' : '' }}">
                        <i class="ri-git-branch-line me-2"></i>
                        Lead Distribution Report
                    </a>
                </li>

                {{-- Communication Report --}}
                <li class="nav-item">
                    <a href="{{ route('reports.communications') }}"
                       class="nav-link {{ request()->is('reports/communications') ? 'active' : '' }}">
                        <i class="ri-mail-send-line me-2"></i>
                        Communication Report
                    </a>
                </li>

            </ul>
        </div>
    </li>
@endif
		
					<!-- CRON JOBS -->
	        @if(in_array($currentRoleSlug, ['root','sadmin']))
					<li class="nav-item">
						<a href="#menuCronJobs" class="nav-link" data-bs-toggle="collapse"
						   aria-expanded="false">
						   <i class="ri-restart-line me-2"></i>
						   <span>Cron Jobs</span>
						</a>

						<div class="collapse menu-dropdown" id="menuCronJobs">
							<ul class="nav nav-sm flex-column">

								<li class="nav-item">
									<a href="javascript:void(0)" onclick="runCronAssign()" class="nav-link text-warning">
										<i class="ri-refresh-line me-2"></i> Run Auto Assign
									</a>
								</li>

								<li class="nav-item">
									<a href="javascript:void(0)" onclick="runCronReassign()" class="nav-link text-danger">
										<i class="ri-loop-left-line me-2"></i> Run Auto Reassign
									</a>
								</li>

								<li class="nav-item">
									<a href="javascript:void(0)" onclick="runCronRawLeads()" class="nav-link text-primary">
										<i class="ri-file-list-3-line me-2"></i> Process Raw Lead Uploads
									</a>
								</li>

							<li class="nav-item">
    <a href="javascript:void(0)" onclick="runCronSendSms()" class="nav-link text-info">
        <i class="ri-message-2-line me-2"></i> Send Pending SMS
    </a>
</li>

<li class="nav-item">
    <a href="javascript:void(0)" onclick="runCronSendEmail()" class="nav-link text-success">
        <i class="ri-mail-send-line me-2"></i> Send Pending Email
    </a>
</li>

<li class="nav-item">
    <a href="javascript:void(0)" onclick="runCronSendWhatsapp()" class="nav-link text-success">
        <i class="ri-whatsapp-line me-2"></i> Send Pending WhatsApp
    </a>
</li>
<li class="nav-item">
    <a href="javascript:void(0)" onclick="runCronLeadUploads()" class="nav-link text-primary">
        <i class="ri-upload-cloud-2-line me-2"></i> Process Uploads Leads Data 
    </a>
</li>				 
						</ul>
						</div>
					</li>
			@endif



            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>

<!-- Left Sidebar End -->
<div class="vertical-overlay"></div>
<script>
function runCronAssign() {
    if (!confirm("Run auto-assign now?")) return;

    fetch("{{ url('cron/run-auto-assign') }}")
        .then(r => r.text())
        .then(msg => alert(msg));
}

function runCronReassign() {
    if (!confirm("Run auto-reassign now?")) return;

    fetch("{{ url('cron/run-auto-reassign') }}")
        .then(r => r.text())
        .then(msg => alert(msg));
}

function runCronRawLeads() {
    if (!confirm("Process pending raw lead uploads now?")) return;

    fetch("{{ url('cron/run-raw-leads-process') }}")
        .then(r => r.text())
        .then(msg => alert(msg));
}

function runCronSendSms() {
    if (!confirm("Send pending SMS now?")) return;
    fetch("{{ url('cron/run-send-sms') }}").then(r => r.text()).then(msg => alert(msg));
}

function runCronSendEmail() {
    if (!confirm("Send pending Email now?")) return;
    fetch("{{ url('cron/run-send-email') }}").then(r => r.text()).then(msg => alert(msg));
}

function runCronSendWhatsapp() {
    if (!confirm("Send pending WhatsApp now?")) return;
    fetch("{{ url('cron/run-send-whatsapp') }}").then(r => r.text()).then(msg => alert(msg));
}
</script>
<script>
function runCronLeadUploads() {
    if (!confirm("Process pending lead uploads now?")) return;

    fetch("{{ url('cron/run-lead-uploads') }}")
        .then(r => r.text())
        .then(msg => alert(msg));
}
</script>