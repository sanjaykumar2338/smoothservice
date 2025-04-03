<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
   <div class="app-brand demo">
      
      @php
         $company_settings = App\Models\CompanySetting::where('user_id', auth()->id())->first();
         $sidebar_logo_exists = $company_settings && $company_settings->sidebar_logo && file_exists(public_path('storage/' . $company_settings->sidebar_logo));
         $favicon_exists = $company_settings && $company_settings->favicon && file_exists(public_path('storage/' . $company_settings->favicon));
      @endphp

      <a href="{{ route('dashboard') }}" class="app-brand-link">
         <style>
            .sidebar-logo {
               display: block;
               margin: 0 auto;
               max-width: 100%;
               height: 54%; /* Default height */
               width: 150px; /* Default width */
               transition: all 0.3s ease;
            }

            .app-brand-link {
               display: flex;             /* Flexbox layout for centering */
               flex-direction: column;    /* Ensures vertical stacking */
               align-items: center;       /* Horizontal centering */
               justify-content: center;   /* Vertical centering */
               height: 120px;             /* Set a height for the logo container */
               padding: 10px;             /* Add some padding for better spacing */
               text-align: center;        /* Centers any fallback text */
            }

            /* Adjustments for collapsed menu */
            html.layout-menu-collapsed .sidebar-logo {
               height: 32px; /* Shrink to favicon size */
               width: 32px;
            }

            html.layout-menu-collapsed .app-brand-link {
               height: 80px; /* Adjust height for collapsed menu */
            }
         </style>

         @if($sidebar_logo_exists)
            <!-- Sidebar Logo -->
            <img src="{{ asset('storage/' . $company_settings->sidebar_logo) }}" 
               data-collapsed-src="{{ $favicon_exists ? asset('storage/' . $company_settings->favicon) : '' }}" 
               class="sidebar-logo" alt="Sidebar Logo" id="sidebarLogo">
         @elseif($company_settings && $company_settings->company_name)
            <span class="app-brand-text demo fs-4">{{ $company_settings->company_name }}</span>
         @else
            <span class="app-brand-text demo fs-4">{{ env('APP_NAME') }}</span>
         @endif
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
      <li class="menu-item @php echo in_array(request()->route()->getName(), [
            'service.list', 
            'service.add', 
            'service.edit', 
            'service.intakeform.list', 
            'service.intakeform.add', 
            'service.intakeform.edit', 
            'team.list', 
            'team.add', 
            'team.edit', 
            'landingpage.list', 
            'landingpage.add', 
            'landingpage.edit'
         ]) ? 'open' : '' @endphp">
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
            <li class="menu-item {{in_array(request()->route()->getName(), ['landingpage.list', 'landingpage.add', 'landingpage.edit']) ? 'active' : ''}}">
               <a href="{{route('landingpage.list')}}" class="menu-link">
                  <div data-i18n="Landing Pages">Landing Pages</div>
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

<script>
   document.addEventListener("DOMContentLoaded", function () {
      const html = document.documentElement; // Access the <html> tag
      const sidebarLogo = document.getElementById("sidebarLogo");

      // Function to update the logo based on the collapsed state
      const updateLogo = () => {
         if (html.classList.contains("layout-menu-collapsed")) {
               // Switch to the favicon when collapsed
               sidebarLogo.src = sidebarLogo.getAttribute("data-collapsed-src");
               sidebarLogo.style.height = "32px"; // Adjust size
               sidebarLogo.style.width = "32px";
         } else {
               // Switch back to the sidebar logo when expanded
               //sidebarLogo.src = "{{ asset('storage/' . $company_settings->sidebar_logo) }}";
               sidebarLogo.src = "{{ $company_settings ? asset('storage/' . $company_settings->sidebar_logo) : asset('default-logo.png') }}";

               sidebarLogo.style.height = "54%"; // Reset to default size
               sidebarLogo.style.width = "150px";
         }
      };

      // Initial check on page load
      updateLogo();

      // Monitor changes to the <html> class
      new MutationObserver(updateLogo).observe(html, {
         attributes: true,
         attributeFilter: ["class"],
      });
   });

</script>