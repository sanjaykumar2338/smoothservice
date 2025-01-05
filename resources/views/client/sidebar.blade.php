<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
   <div class="app-brand demo">
      <a href="{{route('dashboard')}}" class="app-brand-link">
         <span class="app-brand-logo demo">
            <svg width="26px" height="26px" viewBox="0 0 26 26" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
               <title>icon</title>
               <defs>
                  <linearGradient x1="50%" y1="0%" x2="50%" y2="100%" id="linearGradient-1">
                     <stop stop-color="#5A8DEE" offset="0%"></stop>
                     <stop stop-color="#699AF9" offset="100%"></stop>
                  </linearGradient>
                  <linearGradient x1="0%" y1="0%" x2="100%" y2="100%" id="linearGradient-2">
                     <stop stop-color="#FDAC41" offset="0%"></stop>
                     <stop stop-color="#E38100" offset="100%"></stop>
                  </linearGradient>
               </defs>
               <g id="Pages" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                  <g id="Login---V2" transform="translate(-667.000000, -290.000000)">
                     <g id="Login" transform="translate(519.000000, 244.000000)">
                        <g id="Logo" transform="translate(148.000000, 42.000000)">
                           <g id="icon" transform="translate(0.000000, 4.000000)">
                              <path d="M13.8863636,4.72727273 C18.9447899,4.72727273 23.0454545,8.82793741 23.0454545,13.8863636 C23.0454545,18.9447899 18.9447899,23.0454545 13.8863636,23.0454545 C8.82793741,23.0454545 4.72727273,18.9447899 4.72727273,13.8863636 C4.72727273,13.5423509 4.74623858,13.2027679 4.78318172,12.8686032 L8.54810407,12.8689442 C8.48567157,13.19852 8.45300462,13.5386269 8.45300462,13.8863636 C8.45300462,16.887125 10.8856023,19.3197227 13.8863636,19.3197227 C16.887125,19.3197227 19.3197227,16.887125 19.3197227,13.8863636 C19.3197227,10.8856023 16.887125,8.45300462 13.8863636,8.45300462 C13.5386269,8.45300462 13.19852,8.48567157 12.8689442,8.54810407 L12.8686032,4.78318172 C13.2027679,4.74623858 13.5423509,4.72727273 13.8863636,4.72727273 Z" id="Combined-Shape" fill="#4880EA"></path>
                              <path d="M13.5909091,1.77272727 C20.4442608,1.77272727 26,7.19618701 26,13.8863636 C26,20.5765403 20.4442608,26 13.5909091,26 C6.73755742,26 1.18181818,20.5765403 1.18181818,13.8863636 C1.18181818,13.540626 1.19665566,13.1982714 1.22574292,12.8598734 L6.30410592,12.859962 C6.25499466,13.1951893 6.22958398,13.5378796 6.22958398,13.8863636 C6.22958398,17.8551125 9.52536149,21.0724191 13.5909091,21.0724191 C17.6564567,21.0724191 20.9522342,17.8551125 20.9522342,13.8863636 C20.9522342,9.91761479 17.6564567,6.70030817 13.5909091,6.70030817 C13.2336969,6.70030817 12.8824272,6.72514561 12.5388136,6.77314791 L12.5392575,1.81561642 C12.8859498,1.78721495 13.2366963,1.77272727 13.5909091,1.77272727 Z" id="Combined-Shape2" fill="url(#linearGradient-1)"></path>
                              <rect id="Rectangle" fill="url(#linearGradient-2)" x="0" y="0" width="7.68181818" height="7.68181818"></rect>
                           </g>
                        </g>
                     </g>
                  </g>
               </g>
            </svg>
         </span>
         <span class="app-brand-text demo menu-text fs-4">{{env('APP_NAME')}}</span>
      </a>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <i class="bx menu-toggle-icon d-none d-xl-block fs-4 align-middle"></i>
      <i class="bx bx-x d-block d-xl-none bx-sm align-middle"></i>
      </a>
   </div>
   <div class="menu-divider mt-0"></div>
   <div class="menu-inner-shadow"></div>
   <ul class="menu-inner py-1">
      <li class="menu-header small text-uppercase">
         <span class="menu-header-text" data-i18n="Activity">Activity</span>
      </li>
      <!-- Dashboards -->
      <li class="menu-item {{request()->route()->getName() == 'dashboard' ? 'open active' : ''}}">
         <a href="{{route('dashboard')}}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-home-circle"></i>
            <div data-i18n="Dashboard">Dashboard</div>
         </a>
      </li>
      <!-- Orders -->
      <li class="menu-item @php echo in_array(request()->route()->getName(), ['order.list', 'order.add', 'order.edit', 'order.show', 'order.project_data']) ? 'open active' : ''@endphp">
         <a href="{{ route('order.list') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cart"></i>
            <div data-i18n="Orders">Orders</div>
         </a>
      </li>
      <!-- Tickets -->
      <li class="menu-item @php echo in_array(request()->route()->getName(), ['ticket.list', 'ticket.add', 'ticket.edit', 'ticket.show']) ? 'open active' : ''@endphp">
          <a href="{{ route('ticket.list') }}" class="menu-link">
              <i class='menu-icon tf-icons bx bx-clipboard'></i>
              <div data-i18n="Tickets">Tickets</div>
          </a>
      </li>

      <!-- Clients -->
      <li class="menu-item @php echo in_array(request()->route()->getName(), ['client.list', 'client.add', 'client.edit']) ? 'open active' : ''@endphp">
         <a href="{{ route('client.list') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div data-i18n="Clients">Clients</div>
         </a>
      </li>
      <!-- billing -->
      <li class="menu-header small text-uppercase">
         <span class="menu-header-text" data-i18n="Billing">Billing</span>
      </li>
      <li class="menu-item {{ request()->routeIs(['invoices.list', 'invoices.create', 'invoices.edit', 'invoices.update', 'invoices.destroy', 'invoices.show']) ? 'active' : '' }}">
         <a href="{{ route('invoices.list') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-receipt"></i> <!-- Invoice icon -->
            <div data-i18n="Invoices">Invoices</div>
         </a>
      </li>
      <li class="menu-item {{ request()->routeIs(['subscriptions.list', 'subscriptions.create', 'subscriptions.edit', 'subscriptions.update', 'subscriptions.destroy', 'subscriptions.show']) ? 'active' : '' }}">
         <a href="{{ route('subscriptions.list') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-book"></i> <!-- Subscription icon, you can replace it if needed -->
            <div data-i18n="Subscriptions">Subscriptions</div>
         </a>
      </li>
      <!-- Marketing -->
      <li class="menu-header small text-uppercase">
         <span class="menu-header-text" data-i18n="Marketing">Marketing</span>
      </li>
      <li class="menu-item {{ request()->routeIs(['coupon.list', 'coupon.create', 'coupon.edit', 'coupon.update', 'coupon.destroy', 'coupon.show']) ? 'active' : '' }}">
         <a href="{{ route('coupon.list') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-purchase-tag"></i> <!-- Coupon icon -->
            <div data-i18n="Coupons">Coupons</div>
         </a>
      </li>
      <!-- Setup and settings -->
      <li class="menu-header small text-uppercase">
         <span class="menu-header-text" data-i18n="Setup">Setup</span>
      </li>

      <!-- Services -->
      <li class="menu-item @php echo in_array(request()->route()->getName(), ['service.list', 'service.add', 'service.edit', 'service.intakeform.list', 'service.intakeform.add', 'service.intakeform.edit', 'team.list', 'team.add', 'team.edit']) ? 'open' : ''@endphp">
         <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-briefcase"></i>
            <div data-i18n="Services">Services</div>
         </a>
         <ul class="menu-sub">
            <li class="menu-item {{in_array(request()->route()->getName(), ['service.list', 'service.add', 'service.edit']) ? 'active' : ''}}">
               <a href="{{route('service.list')}}" class="menu-link">
                  <div data-i18n="Services List">Services List</div>
               </a>
            </li>
            <li class="menu-item {{in_array(request()->route()->getName(), ['service.intakeform.list', 'service.intakeform.add', 'service.intakeform.edit']) ? 'active' : ''}}">
               <a href="{{route('service.intakeform.list')}}" class="menu-link">
                  <div data-i18n="Intake Forms">Intake Forms</div>
               </a>
            </li>
            <li class="menu-item {{in_array(request()->route()->getName(), ['team.list', 'team.add', 'team.edit']) ? 'active' : ''}}">
               <a href="{{route('team.list')}}" class="menu-link">
                  <div data-i18n="Team Members">Team Members</div>
               </a>
            </li>
            <li class="menu-item">
               <a href="#" class="menu-link">
                  <div data-i18n="Landing pages">Landing pages</div>
               </a>
            </li>
         </ul>
      </li>

      <!-- integrations -->
      <li class="menu-item {{ request()->routeIs(['integrations','integrations.stripe.connect','integrations.paypal']) ? 'active' : '' }}">
         <a href="{{ route('integrations') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-plug"></i>
            <div data-i18n="Integrations">Integrations</div>
         </a>
      </li>

      <!-- Settings -->
      <li class="menu-item {{request()->routeIs([
               'setting.orderstatuses.list', 
               'setting.orderstatuses.create', 
               'setting.orderstatuses.edit', 
               'setting.orderstatuses.update', 
               'setting.orderstatuses.delete', 
               'tags.list', 
               'tags.create', 
               'tags.edit', 
               'statuses.list', 
               'statuses.create', 
               'statuses.edit', 
               'roles.list', 
               'roles.create', 
               'roles.edit', 
               'setting.ticketstatuses.list', 
               'setting.ticketstatuses.create', 
               'setting.ticketstatuses.edit', 
               'setting.ticketstatuses.update', 
               'setting.ticketstatuses.delete',
               'tickettags.list',
               'tickettags.create',
               'tickettags.edit',
               'company.list',
               'company.edit',
            ]) ? 'open active' : ''}}">
         <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div data-i18n="Settings">Settings</div>
         </a>
         <ul class="menu-sub">
            
            <li class="menu-item {{request()->routeIs(['setting.orderstatuses.list', 'setting.orderstatuses.create', 'setting.orderstatuses.edit', 'setting.orderstatuses.update', 'setting.orderstatuses.delete']) ? 'active' : ''}}">
               <a href="{{route('setting.orderstatuses.list')}}" class="menu-link">
                  <div data-i18n="Order Statuses">Order Statuses</div>
               </a>
            </li>

            <li class="menu-item {{request()->routeIs(['tags.list', 'tags.create', 'tags.edit']) ? 'active' : ''}}">
               <a href="{{route('tags.list')}}" class="menu-link">
                  <div data-i18n="Order Tags">Order Tags</div>
               </a>
            </li>

            <li class="menu-item {{request()->routeIs(['statuses.list', 'statuses.create', 'statuses.edit']) ? 'active' : ''}}">
               <a href="{{route('statuses.list')}}" class="menu-link">
                  <div data-i18n="Client Statuses">Client Statuses</div>
               </a>
            </li>

            <li class="menu-item {{request()->routeIs(['setting.ticketstatuses.list', 'setting.ticketstatuses.create', 'setting.ticketstatuses.edit', 'setting.ticketstatuses.update', 'setting.ticketstatuses.delete']) ? 'active' : ''}}">
               <a href="{{route('setting.ticketstatuses.list')}}" class="menu-link">
                  <div data-i18n="Ticket Statuses">Ticket Statuses</div>
               </a>
            </li>

            <li class="menu-item {{request()->routeIs(['tickettags.list', 'tickettags.create', 'tickettags.edit']) ? 'active' : ''}}">
               <a href="{{route('tickettags.list')}}" class="menu-link">
                  <div data-i18n="Ticket Tags">Ticket Tags</div>
               </a>
            </li>


            <li class="menu-item {{request()->routeIs(['roles.list', 'roles.create', 'roles.edit']) ? 'active' : ''}}">
               <a href="{{route('roles.list')}}" class="menu-link">
                  <div data-i18n="Roles">Roles</div>
               </a>
            </li>
          
            <li class="menu-item {{ request()->routeIs(['company.list', 'company.edit']) ? 'active' : '' }}">
               <a href="{{ route('company.list') }}" class="menu-link">
                  <div data-i18n="Company">Company</div>
               </a>
            </li>

         </ul>
      </li>
   </ul>
</aside>