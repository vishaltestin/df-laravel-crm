

@php
  $role = $role ?? null;
  $orgId = $orgId ?? null;
  
 @endphp






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

                <!-- Leads Manager -->
                <li class="nav-item {{ request()->is('leads*') ? 'active' : '' }}">
                    <a class="nav-link menu-link {{ request()->is('leads*') ? 'active' : '' }}"
                       href="#menuLeads" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->is('leads*') ? 'true' : 'false' }}"
                       aria-controls="menuLeads">
                       <i class="ri-contacts-book-line me-2"></i> 
                       <span>Leads Manager</span>
                    </a>

                    <div class="menu-dropdown collapse {{ request()->is('leads*') ? 'show' : '' }}" id="menuLeads">
                        <ul class="nav nav-sm flex-column">

                            {{-- Lead List --}}
                            <li class="nav-item">
                                <a href="{{ route('Leads.ListView') }}" class="nav-link">
                                    <i class="ri-list-check me-2"></i> 
                                    All Leads
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

        <!-- AUTOMATION LEADS -->
<li class="nav-item {{ request()->is('automation/raw-leads*') ? 'active' : '' }}">
    <a class="nav-link menu-link {{ request()->is('automation/raw-leads*') ? 'active' : '' }}"
       href="#menuAutomationLeads" data-bs-toggle="collapse"
       aria-expanded="{{ request()->is('automation/raw-leads*') ? 'true' : 'false' }}"
       aria-controls="menuAutomationLeads">
        <i class="ri-robot-line me-2"></i>
        <span>Automation Leads</span>
    </a>

    <div class="menu-dropdown collapse {{ request()->is('automation/raw-leads*') ? 'show' : '' }}"
         id="menuAutomationLeads">
        <ul class="nav nav-sm flex-column">

            <!-- Raw Leads / Automation Panel -->
            <li class="nav-item {{ request()->is('automation/raw-leads') ? 'active' : '' }}">
                <a href="{{ url('automation/raw-leads') }}"
                   class="nav-link {{ request()->is('automation/raw-leads') ? 'active' : '' }}">
                   <i class="ri-stack-line me-2"></i> Raw Leads Panel
                </a>
            </li>

            <!-- IVR Call Logs -->
            <li class="nav-item {{ request()->is('automation/ivr*') ? 'active' : '' }}">
                <a href="{{ url('automation/logs/ivr') }}"
                   class="nav-link {{ request()->is('automation/ivr*') ? 'active' : '' }}">
                   <i class="ri-phone-fill me-2"></i> IVR Call Logs
                </a>
            </li>

            <!-- WhatsApp Logs -->
            <li class="nav-item {{ request()->is('automation/whatsapp*') ? 'active' : '' }}">
                <a href="{{ url('automation/logs/whatsapp') }}"
                   class="nav-link {{ request()->is('automation/whatsapp*') ? 'active' : '' }}">
                   <i class="ri-whatsapp-fill me-2"></i> WhatsApp Logs
                </a>
            </li>

            <!-- SMS Logs -->
            <li class="nav-item {{ request()->is('automation/sms*') ? 'active' : '' }}">
                <a href="{{ url('automation/logs/sms') }}"
                   class="nav-link {{ request()->is('automation/sms*') ? 'active' : '' }}">
                   <i class="ri-message-fill me-2"></i> SMS Logs
                </a>
            </li>
            <!-- Verified Leads -->
            <li class="nav-item {{ request()->is('automation/verified-leads*') ? 'active' : '' }}">
                <a href="{{ url('automation/verified-leads') }}"
                   class="nav-link {{ request()->is('automation/verified-leads*') ? 'active' : '' }}">
                   <i class="ri-checkbox-circle-line me-2"></i> Verified Leads
                </a>
            </li>

            <!-- Rejected Leads -->
            <li class="nav-item {{ request()->is('automation/rejected-leads*') ? 'active' : '' }}">
                <a href="{{ url('automation/rejected-leads') }}"
                   class="nav-link {{ request()->is('automation/rejected-leads*') ? 'active' : '' }}">
                   <i class="ri-close-circle-line me-2"></i> Rejected Leads
                </a>
            </li>

        </ul>
    </div>
</li>

        <!-- FORM DESK -->
                <li class="nav-item {{ request()->is('email-templates*') || request()->is('webforms*') ? 'active' : '' }}">
                    <a class="nav-link menu-link {{ request()->is('email-templates*') || request()->is('webforms*') ? 'active' : '' }}"
                       href="#menuFormDesk" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->is('email-templates*') || request()->is('webforms*') ? 'true' : 'false' }}"
                       aria-controls="menuFormDesk">
                        <i class="ri-pages-line me-2"></i> 
                        <span>FormDesk</span>
                    </a>

                    <div class="menu-dropdown collapse {{ request()->is('email-templates*') || request()->is('webforms*') ? 'show' : '' }}"
                        id="menuFormDesk">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item {{ request()->is('webforms*') ? 'active' : '' }}">
                                <a href="{{ url('webforms') }}" class="nav-link {{ request()->is('webforms*') ? 'active' : '' }}">
                                    <i class="ri-file-list-3-line me-2"></i> 
                                    Manage Forms
                                </a>
                            </li>

                            <li class="nav-item {{ request()->is('webforms*') ? 'active' : '' }}">
                                <a href="{{ url('webforms') }}" class="nav-link {{ request()->is('design-template*') ? 'active' : '' }}">
                                    <i class="ri-palette-line me-2"></i> 
                                    Template Designs
                                </a>
                            </li>

                            <li class="nav-item {{ request()->is('webforms*') ? 'active' : '' }}">
                                <a href="{{ url('webforms') }}" class="nav-link {{ request()->is('application-webforms*') ? 'active' : '' }}">
                                    <i class="ri-article-line me-2"></i> 
                                    Application forms
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <!-- COURSES MANAGER -->
                <li class="nav-item {{ request()->is('products*') ? 'active' : '' }}">
                    <a class="nav-link menu-link {{ request()->is('products*') ? 'active' : '' }}"
                       href="#menuCourses" data-bs-toggle="collapse"
                       aria-expanded="{{ request()->is('products*') ? 'true' : 'false' }}"
                       aria-controls="menuCourses">
                        <i class="ri-book-2-line me-2"></i> 
                        <span>Courses Manager</span>
                    </a>

                    <div class="menu-dropdown collapse {{ request()->is('products*') ? 'show' : '' }}" id="menuCourses">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item {{ request()->is('products*') ? 'active' : '' }}">
                                <a href="{{ url('products') }}" class="nav-link {{ request()->is('products*') ? 'active' : '' }}">
                                    <i class="ri-book-open-line me-2"></i> 
                                    Courses
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- USER ACCESS CONTROL -->
                <li class="nav-item {{ request()->is('user*') || request()->is('role*') || request()->is('teams*') || request()->is('designations*') || request()->is('organizations*') ? 'active' : '' }}">
                    <a class="nav-link menu-link {{ request()->is('user*') || request()->is('role*') || request()->is('teams*') || request()->is('designations*') || request()->is('organizations*') ? 'active' : '' }}"
                        href="#menuUAC" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->is('user*') || request()->is('role*') || request()->is('teams*') || request()->is('designations*') || request()->is('organizations*') ? 'true' : 'false' }}"
                        aria-controls="menuUAC">
                        <i class="ri-shield-user-fill me-2"></i> 
                        <span>User Access Control</span>
                    </a>

                    <div class="menu-dropdown collapse {{ request()->is('user*') || request()->is('role*') || request()->is('teams*') || request()->is('designations*') || request()->is('organizations*') ? 'show' : '' }}"
                        id="menuUAC">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item {{ request()->is('user*') ? 'active' : '' }}">
                                <a href="{{ url('user') }}" class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                                    <i class="ri-user-line me-2"></i> Manage Users
                                </a>
                            </li>

                            <li class="nav-item {{ request()->is('role*') ? 'active' : '' }}">
                                <a href="{{ url('role') }}" class="nav-link {{ request()->is('role*') ? 'active' : '' }}">
                                    <i class="ri-lock-line me-2"></i> Manage Permissions
                                </a>
                            </li>

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
                                    <i class=" ri-home-3-fill  me-2"></i> Organizations
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <!-- MASTER DATA -->
                <li class="nav-item {{ request()->is('pipelines*') || request()->is('sources*') || request()->is('types*') || request()->is('attributes*') ? 'active' : '' }}">
                    <a class="nav-link menu-link {{ request()->is('pipelines*') || request()->is('sources*') || request()->is('types*') || request()->is('attributes*') ? 'active' : '' }}"
                        href="#menuMasterData" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->is('pipelines*') || request()->is('sources*') || request()->is('types*') || request()->is('attributes*') ? 'true' : 'false' }}"
                        aria-controls="menuMasterData">
                        <i class="ri-database-2-line me-2"></i> 
                        <span>Master Data</span>
                    </a>

                    <div class="menu-dropdown collapse {{ request()->is('pipelines*') || request()->is('sources*') || request()->is('types*') || request()->is('attributes*') ? 'show' : '' }}"
                        id="menuMasterData">

                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item {{ request()->is('pipelines*') ? 'active' : '' }}">
                                <a href="{{ url('pipelines') }}" class="nav-link {{ request()->is('pipelines*') ? 'active' : '' }}">
                                    <i class=" ri-ancient-gate-line  me-2"></i> Pipelines
                                </a>
                            </li>

                            <li class="nav-item {{ request()->is('sources*') ? 'active' : '' }}">
                                <a href="{{ url('sources') }}" class="nav-link {{ request()->is('sources*') ? 'active' : '' }}">
                                    <i class="ri-links-line me-2"></i> Lead Sources
                                </a>
                            </li>

                            <li class="nav-item {{ request()->is('types*') ? 'active' : '' }}">
                                <a href="{{ url('types') }}" class="nav-link {{ request()->is('types*') ? 'active' : '' }}">
                                    <i class="ri-price-tag-2-line me-2"></i> Lead Types
                                </a>
                            </li>

                            <li class="nav-item {{ request()->is('attributes*') ? 'active' : '' }}">
                                <a href="{{ url('attributes') }}" class="nav-link {{ request()->is('attributes*') ? 'active' : '' }}">
                                    <i class="ri-grid-line me-2"></i> Attributes
                                </a>
                            </li>

                            <li class="nav-item {{ request()->is('webhooks*') ? 'active' : '' }}">
                                <a href="{{ url('webhooks') }}" class="nav-link {{ request()->is('webhooks*') ? 'active' : '' }}">
                                    <i class="ri-git-pull-request-line me-2"></i> Webhooks
                                </a>
                            </li>

                        </ul>
                    </div>
                </li>

                <!-- TEMPLATE MANAGER -->
             <!-- TEMPLATE MANAGER -->
<li class="nav-item {{ request()->is('email-templates*') || request()->is('whatsapp-templates*') || request()->is('message-templates*') ? 'active' : '' }}">
    <a class="nav-link menu-link {{ request()->is('email-templates*') || request()->is('whatsapp-templates*') || request()->is('message-templates*') ? 'active' : '' }}"
        href="#menuTemplates" data-bs-toggle="collapse"
        aria-expanded="{{ request()->is('email-templates*') || request()->is('whatsapp-templates*') || request()->is('message-templates*') ? 'true' : 'false' }}"
        aria-controls="menuTemplates">
        <i class="ri-folder-line me-2"></i>
        <span>Template Manager</span>
    </a>

    <div class="menu-dropdown collapse {{ request()->is('email-templates*') || request()->is('whatsapp-templates*') || request()->is('message-templates*') ? 'show' : '' }}"
        id="menuTemplates">

        <ul class="nav nav-sm flex-column">

            <!-- Email Templates -->
            <li class="nav-item {{ request()->is('email-templates*') ? 'active' : '' }}">
                <a href="{{ url('email-templates') }}" class="nav-link {{ request()->is('email-templates*') ? 'active' : '' }}">
                    <i class="ri-mail-send-line me-2"></i> Email Templates
                </a>
            </li>

            <!-- WhatsApp Templates -->
            <li class="nav-item {{ request()->is('whatsapp-templates*') ? 'active' : '' }}">
                <a href="{{ url('whatsapp-templates') }}" class="nav-link {{ request()->is('whatsapp-templates*') ? 'active' : '' }}">
                    <i class="ri-whatsapp-line me-2"></i> WhatsApp Templates
                </a>
            </li>

            <!-- SMS Templates -->
            <li class="nav-item {{ request()->is('message-templates*') ? 'active' : '' }}">
                <a href="{{ url('message-templates') }}" class="nav-link {{ request()->is('message-templates*') ? 'active' : '' }}">
                    <i class="ri-chat-1-line me-2"></i> SMS Templates
                </a>
            </li>

        </ul>

    </div>
</li>


                <!-- SETTINGS -->
                <li class="nav-item {{ request()->is('general-settings*') || request()->is('graphics-settings*') || request()->is('email-settings*') || request()->is('whatsapp-settings*') || request()->is('sms-settings*') || request()->is('ivr-settings*') || request()->is('webhooks*') ? 'active' : '' }}">
                    <a class="nav-link menu-link {{ request()->is('general-settings*') || request()->is('graphics-settings*') || request()->is('email-settings*') || request()->is('whatsapp-settings*') || request()->is('sms-settings*') || request()->is('ivr-settings*') || request()->is('webhooks*') ? 'active' : '' }}"
                        href="#menuSettings" data-bs-toggle="collapse"
                        aria-expanded="{{ request()->is('general-settings*') || request()->is('graphics-settings*') || request()->is('email-settings*') || request()->is('whatsapp-settings*') || request()->is('sms-settings*') || request()->is('ivr-settings*') || request()->is('webhooks*') ? 'true' : 'false' }}"
                        aria-controls="menuSettings">
                        <i class="ri-settings-3-fill me-2"></i> 
                        <span>Settings</span>
                    </a>

                    <div class="menu-dropdown collapse {{ request()->is('general-settings*') || request()->is('graphics-settings*') || request()->is('email-settings*') || request()->is('whatsapp-settings*') || request()->is('sms-settings*') || request()->is('ivr-settings*') || request()->is('webhooks*') ? 'show' : '' }}" id="menuSettings">
                        <ul class="nav nav-sm flex-column">

                            <li class="nav-item">
                                <a href="#menuConfig" class="nav-link" data-bs-toggle="collapse"
                                   aria-expanded="{{ request()->is('general-settings*') || request()->is('graphics-settings*') || request()->is('email-settings*') || request()->is('whatsapp-settings*') || request()->is('sms-settings*') || request()->is('ivr-settings*') ? 'true' : 'false' }}"
                                   aria-controls="menuConfig">
                                    <i class="ri-tools-line me-2"></i> Configuration
                                </a>

                                <div class="menu-dropdown collapse {{ request()->is('general-settings*') || request()->is('graphics-settings*') || request()->is('email-settings*') || request()->is('whatsapp-settings*') || request()->is('sms-settings*') || request()->is('ivr-settings*') ? 'show' : '' }}" id="menuConfig">
                                    <ul class="nav nav-sm flex-column">

                                        <li class="nav-item {{ request()->is('general-settings*') ? 'active' : '' }}">
                                            <a href="{{ url('general-settings') }}" class="nav-link {{ request()->is('general-settings*') ? 'active' : '' }}">
                                                <i class="ri-settings-4-line me-2"></i> General Settings
                                            </a>
                                        </li>

                                        <li class="nav-item {{ request()->is('graphics-settings*') ? 'active' : '' }}">
                                            <a data-bs-toggle="offcanvas" data-bs-target="#theme-settings-offcanvas" aria-controls="theme-settings-offcanvas" class="nav-link {{ request()->is('graphics-settings*') ? 'active' : '' }}">
                                                <i class="ri-image-edit-line me-2"></i> Graphics Settings
                                            </a>
                                        </li>

                                        <li class="nav-item {{ request()->is('email-settings*') ? 'active' : '' }}">
                                            <a href="{{ url('email-settings') }}" class="nav-link {{ request()->is('email-settings*') ? 'active' : '' }}">
                                                <i class="ri-mail-settings-line me-2"></i> Email Settings
                                            </a>
                                        </li>

                                        <li class="nav-item {{ request()->is('whatsapp-settings*') ? 'active' : '' }}">
                                            <a href="{{ url('whatsapp-settings') }}" class="nav-link {{ request()->is('whatsapp-settings*') ? 'active' : '' }}">
                                                <i class="ri-whatsapp-line me-2"></i> WhatsApp Settings
                                            </a>
                                        </li>

                                        <li class="nav-item {{ request()->is('sms-settings*') ? 'active' : '' }}">
                                            <a href="{{ url('sms-settings') }}" class="nav-link {{ request()->is('sms-settings*') ? 'active' : '' }}">
                                                <i class="ri-message-line me-2"></i> SMS Settings
                                            </a>
                                        </li>

                                        <li class="nav-item {{ request()->is('ivr-settings*') ? 'active' : '' }}">
                                            <a href="{{ url('ivr-settings') }}" class="nav-link {{ request()->is('ivr-settings*') ? 'active' : '' }}">
                                                <i class="ri-phone-line me-2"></i> IVR Settings
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </li>

                            <li class="nav-item">
                                <a href="#menuLeadSettings"
                                   class="nav-link"
                                   data-bs-toggle="collapse"
                                   aria-expanded="{{ request()->is('lead.distribution*') ? 'true' : 'false' }}"
                                   aria-controls="menuLeadSettings">
                                   <i class="ri-settings-5-line me-2"></i> Lead Settings
                                </a>

                                <div class="menu-dropdown collapse {{ request()->is('lead.distribution*') ? 'show' : '' }}" id="menuLeadSettings">
                                    <ul class="nav nav-sm flex-column">

                                           {{-- Lead Status --}}
                                   <li class="nav-item">
										<a class="nav-link" href="{{ url('lead-settings/statuses') }}">
											<i class="ri-list-settings-line"></i> Lead Status
										</a>
									</li>

									{{-- Sub Status (Under Lead Status) --}}
									<li class="nav-item">
										<a class="nav-link" href="{{ url('lead-settings/sub-statuses') }}">
											<i class="ri-node-tree"></i> Lead Sub Status
										</a>
									</li>







										{{-- Lead Rules --}}
                                        <li class="nav-item">
                                            <a href="{{ route('lead.distribution.index') }}" class="nav-link">
                                                <i class="ri-git-branch-line me-2"></i> Rules
                                            </a>
                                        </li>

                                        {{-- Lead Assignment Report --}}
                                        <li class="nav-item">
                                            <a href="{{ route('reports.lead.distribution') }}" class="nav-link">
                                                <i class="ri-line-chart-line me-2"></i> Distribution Report
                                            </a>
                                        </li>

                                        {{-- Manual Cron Trigger --}}
                                        <li class="nav-item">
                                            <a href="javascript:void(0)" onclick="runCronAssign()" class="nav-link text-warning">
                                                <i class="ri-refresh-line me-2"></i> Run Auto Assign (Cron)
                                            </a>
                                        </li>

                                        <li class="nav-item">
                                            <a href="javascript:void(0)" onclick="runCronReassign()" class="nav-link text-danger">
                                                <i class="ri-loop-right-line me-2"></i> Run Auto-Reassign
                                            </a>
                                        </li>

                                    </ul>
                                </div>
                            </li>

                        </ul>
                    </div>
                </li>


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
    fetch('/cron/run-auto-assign')
        .then(r => r.text())
        .then(msg => alert(msg));
}

function runCronReassign() {
    if (!confirm("Run auto-reassign now?")) return;
    fetch('/cron/run-auto-reassign')
        .then(r => r.text())
        .then(msg => alert(msg));
}
</script>
