<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{route('dashboard')}}" class="app-brand-link">
      <span class="app-brand-logo demo">
        <svg width="26px" height="26px" viewBox="0 0 26 26" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
          <!-- SVG content here -->
        </svg>
      </span>
      <span class="menu-text fw-bold ms-1">{{env('APP_NAME')}}</span>
    </a>
  </div>

  <div class="menu-divider mt-0"></div>
  <div class="menu-inner-shadow"></div>

  <ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item {{request()->route()->getName() == 'dashboard' ? 'open active' : ''}}">
      <a href="{{route('dashboard')}}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-home-circle"></i>
        <div data-i18n="Dashboard">Dashboard</div>
      </a>
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

    <!-- Clients -->
    <li class="menu-item @php echo in_array(request()->route()->getName(), ['client.list', 'client.add', 'client.edit']) ? 'open active' : ''@endphp">
      <a href="{{ route('client.list') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-user"></i>
        <div data-i18n="Clients">Clients</div>
      </a>
    </li>

    <!-- Orders -->
    <li class="menu-item @php echo in_array(request()->route()->getName(), ['order.list', 'order.add', 'order.edit', 'order.show', 'order.project_data']) ? 'open active' : ''@endphp">
      <a href="{{ route('order.list') }}" class="menu-link">
        <i class="menu-icon tf-icons bx bx-cart"></i>
        <div data-i18n="Orders">Orders</div>
      </a>
    </li>

    <!-- Setup and settings -->
    <li class="menu-header small text-uppercase">
      <span class="menu-header-text" data-i18n="Setup">Setup</span>
    </li>

    <!-- Settings -->
    <li class="menu-item {{request()->routeIs(['setting.orderstatuses.list', 'setting.orderstatuses.create', 'setting.orderstatuses.edit', 'setting.orderstatuses.update', 'setting.orderstatuses.delete', 'tags.list', 'tags.create', 'tags.edit', 'statuses.list', 'statuses.create', 'statuses.edit', 'roles.list', 'roles.create', 'roles.edit']) ? 'open active' : ''}}">
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

        <li class="menu-item {{request()->routeIs(['statuses.list', 'statuses.create', 'statuses.edit']) ? 'active' : ''}}">
          <a href="{{route('statuses.list')}}" class="menu-link">
            <div data-i18n="Client Statuses">Client Statuses</div>
          </a>
        </li>

        <li class="menu-item {{request()->routeIs(['tags.list', 'tags.create', 'tags.edit']) ? 'active' : ''}}">
          <a href="{{route('tags.list')}}" class="menu-link">
            <div data-i18n="Tags">Tags</div>
          </a>
        </li>

        <li class="menu-item {{request()->routeIs(['roles.list', 'roles.create', 'roles.edit']) ? 'active' : ''}}">
          <a href="{{route('roles.list')}}" class="menu-link">
            <div data-i18n="Roles">Roles</div>
          </a>
        </li>
      </ul>
    </li>
  </ul>
</aside>
