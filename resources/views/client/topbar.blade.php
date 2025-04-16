<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
               
                <ul class="navbar-nav flex-row align-items-center ms-auto">
                  
                  <!-- Style Switcher -->
                  <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                      <i class="bx bx-sm"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                      <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                          <span class="align-middle"><i class="bx bx-sun me-2"></i>Light</span>
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                          <span class="align-middle"><i class="bx bx-moon me-2"></i>Dark</span>
                        </a>
                      </li>
                      <li>
                        <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                          <span class="align-middle"><i class="bx bx-desktop me-2"></i>System</span>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <!-- / Style Switcher-->

                  <!-- Notification -->
                  <li class="nav-item dropdown-notifications navbar-dropdown dropdown me-3 me-xl-2">
                    <a
                      class="nav-link dropdown-toggle hide-arrow"
                      href="javascript:void(0);"
                      data-bs-toggle="dropdown"
                      data-bs-auto-close="outside"
                      aria-expanded="false">
                      <i class="bx bx-bell bx-sm"></i>
                      <span class="badge bg-danger rounded-pill badge-notifications">5</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end py-0">
                      <li class="dropdown-menu-header border-bottom">
                        <div class="dropdown-header d-flex align-items-center py-3">
                          <h5 class="text-body mb-0 me-auto">Notification</h5>
                        </div>
                      </li>
                      <li class="dropdown-notifications-list scrollable-container">

                      @foreach(notifications(5) as $date => $histories)
                        @foreach($histories as $history)
                            @php
                                $prefixes = [
                                    'order_message' => 'Order message ',
                                    'order_note' => 'Order note saved with the following data: ',
                                    'ticket_created' => 'Ticket created with the following data: ',
                                    'order_created' => 'Order created with the following data: ',
                                    'order_updated' => 'Order updated with the following data: ',
                                ];

                                $raw = $history->action_details;
                                $prefix = $prefixes[$history->action_type] ?? '';
                                $json = str_replace($prefix, '', $raw);
                                $data = json_decode($json, true);

                                $messageText = $data['message'] ?? ($data['note'] ?? ($data['subject'] ?? 'N/A'));
                                $orderLink = isset($data['order_id']) ? url('/admin/order/' . $data['order_id']) : null;
                            @endphp

                            <li class="list-group-item list-group-item-action dropdown-notifications-item">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar">
                                            
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ ucwords(str_replace('_', ' ', $history->action_type)) }}</h6>
                                        <p class="mb-0">
                                            {!! $orderLink ? 'Order Message: <a href="' . $orderLink . '">' . $messageText . '</a>' : $messageText !!}
                                        </p>

                                        <small class="text-muted">{{ \Carbon\Carbon::parse($history->created_at)->diffForHumans() }}</small>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    @endforeach


                      </li>
                      <li class="dropdown-menu-footer border-top">
                        <a href="{{route('notifications.list')}}" class="dropdown-item d-flex justify-content-center p-3">
                          View all notifications
                        </a>
                      </li>
                    </ul>
                  </li>
                  <!--/ Notification -->

                  <!-- User -->
                  <li class="nav-item navbar-dropdown dropdown-user dropdown">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                      <div class="avatar avatar-online">
                        <img src="{{ getAuthenticatedUser()->profile_image ? asset(getAuthenticatedUser()->profile_image) : asset('assets/img/avatars/1.png') }}" alt class="rounded-circle" />
                      </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                      <li>
                        <a class="dropdown-item" href="{{route('profile')}}">
                          <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                              <div class="avatar avatar-online">
                                <img src="{{ getAuthenticatedUser()->profile_image ? asset(getAuthenticatedUser()->profile_image) : asset('assets/img/avatars/1.png') }}" alt class="rounded-circle" />
                              </div>
                            </div>
                            <div class="flex-grow-1">
                            @php $user_type = ''; @endphp  
                            @if (Auth::guard('web')->check())
                                @php $user_type = 'Admin'; @endphp  
                                <span class="fw-medium d-block lh-1">{{ Auth::guard('web')->user()->first_name }} {{ Auth::guard('web')->user()->last_name }}</span>
                            @elseif (Auth::guard('team')->check())
                                @php $user_type = 'Team'; @endphp 
                                <span class="fw-medium d-block lh-1">{{ Auth::guard('team')->user()->first_name }} {{ Auth::guard('team')->user()->last_name }}</span>
                            @else
                                @php $user_type = 'Guest'; @endphp 
                                <span class="fw-medium d-block lh-1">Guest</span>
                            @endif
                              <small>{{$user_type}}</small>
                            </div>
                          </div>
                        </a>
                      </li>
                      <li>
                        <div class="dropdown-divider"></div>
                      </li>
                      <li>
                        <a class="dropdown-item" href="{{route('profile')}}">
                          <i class="bx bx-user me-2"></i>
                          <span class="align-middle">Your Profile</span>
                        </a>
                      </li>

                      @if (Session::has('admin_main_id'))
                        <li>
                          <a class="dropdown-item" href="{{route('switch_back_admin')}}">
                            <i class="bx bx-arrow-back"></i>
                            <span class="align-middle">Back to Admin</span>
                          </a>
                        </li>  
                      @endif

                      @if(getUserType()=='web')
                      <li>
                        <a class="dropdown-item" href="{{route('billing')}}">
                          <span class="d-flex align-items-center align-middle">
                            <i class="flex-shrink-0 bx bx-credit-card me-2"></i>
                            <span class="flex-grow-1 align-middle">Billing and Upgrade</span>
                            <span class="flex-shrink-0 badge badge-center rounded-pill bg-danger w-px-20 h-px-20"
                              >4</span
                            >
                          </span>
                        </a>
                      </li>
                      @endif

                      <li>
                        <a class="dropdown-item" href="{{route('logout')}}" target="">
                          <i class="bx bx-power-off me-2"></i>
                          <span class="align-middle">Log Out</span>
                        </a>
                      </li>
                    </ul>
                  </li>
                  <!--/ User -->
                </ul>
              </div>