@extends('layouts/layoutMaster')

@section('title', 'Help Center Landing - Front Pages')

@section('page-style')
<link rel="stylesheet" href="{{asset('assets/vendor/css/pages/front-page-help-center.css')}}" />
@endsection

@section('content')
<!-- Help Center Header -->
<section class="section-py first-section-pt d-flex flex-column justify-content-center align-items-center help-center-header">
  <h3 class="text-center z-1">Hi, how can we help?</h3>
  <div class="input-wrapper my-3 input-group input-group-merge z-1">
    <span class="input-group-text" id="basic-addon1"><i class="bx bx-search-alt bx-xs text-muted"></i></span>
    <input type="text" class="form-control form-control-lg" placeholder="Find anything (features, payment or reset password)" aria-label="Search" aria-describedby="basic-addon1" />
  </div>
  <p class="text-center z-1 px-3 mb-0">Common troubleshooting topics: eCommerce, Blogging to Payment</p>
</section>
<!-- /Help Center Header -->

<!-- Popular Articles -->
<section class="section-py help-center-popular-articles">
  <div class="container">
    <h4 class="text-center mt-2 pb-3">Popular Articles</h4>
    <div class="row">
      <div class="col-lg-10 mx-auto">
        <div class="row mb-3">
          <div class="col-md-4 mb-md-0 mb-4">
            <div class="card border shadow-none">
              <div class="card-body text-center">
                <img class="mb-4" src="{{asset('assets/img/icons/unicons/rocket-square.png')}}" height="48" alt="Help center articles">
                <h5>Getting Started</h5>
                <p> Whether you're new or you're a power user, this article will… </p>
                <a class="btn btn-label-primary" href="{{url('front-pages/help-center-article')}}">Read More</a>
              </div>
            </div>
          </div>
          <div class="col-md-4 mb-md-0 mb-4">
            <div class="card border shadow-none">
              <div class="card-body text-center">
                <img class="mb-4" src="{{asset('assets/img/icons/unicons/cube.png')}}" height="48" alt="Help center articles">
                <h5>First Steps</h5>
                <p> Are you a new customer wondering how to get started? </p>
                <a class="btn btn-label-primary" href="{{url('front-pages/help-center-article')}}">Read More</a>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card border shadow-none">
              <div class="card-body text-center">
                <img class="mb-4" src="{{asset('assets/img/icons/unicons/desktop.png')}}" height="48" alt="Help center articles">
                <h5>Add External Content</h5>
                <p> This article will show you how to expand the functionality of... </p>
                <a class="btn btn-label-primary" href="{{url('front-pages/help-center-article')}}">Read More</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Popular Articles -->

<!-- Knowledge Base -->
<section class="section-py bg-body help-center-knowledge-base help-center-bg-alt">
  <div class="container">
    <h4 class="text-center pb-4">Knowledge Base</h4>
    <div class="row">
      <div class="col-lg-10 mx-auto">
        <div class="row">
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge bg-label-success p-2 rounded me-2"><i class="bx bx-cart bx-sm"></i></span>
                  <h5 class="fw-medium mt-3 ms-1">eCommerce</h5>
                </div>
                <ul>
                  <li class="text-primary py-1"><a href="{{url('front-pages/help-center-article')}}">Pricing Wizard</a></li>
                  <li class="text-primary pb-1"><a href="{{url('front-pages/help-center-article')}}">Square Sync</a></li>
                </ul>
                <p class="mb-0 fw-medium">
                  <a href="{{url('front-pages/help-center-article')}}">56 articles</a>
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge bg-label-info p-2 rounded me-2"><i class="bx bx-laptop bx-sm"></i></span>
                  <h5 class="fw-medium mt-3 ms-1">Building Your Website</h5>
                </div>
                <ul>
                  <li class="text-primary py-1"><a href="{{url('front-pages/help-center-article')}}">First Steps</a></li>
                  <li class="text-primary pb-1"><a href="{{url('front-pages/help-center-article')}}">Add Images</a></li>
                </ul>
                <p class="mb-0 fw-medium">
                  <a href="{{url('front-pages/help-center-article')}}">111 articles</a>
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge bg-label-primary p-2 rounded me-2"><i class="bx bx-user bx-sm"></i></span>
                  <h5 class="fw-medium mt-3 ms-1">Your Account</h5>
                </div>
                <ul>
                  <li class="text-primary py-1"><a href="{{url('front-pages/help-center-article')}}">Insights</a></li>
                  <li class="text-primary pb-1">
                    <a href="{{url('front-pages/help-center-article')}}">Manage Your Orders</a>
                  </li>
                </ul>
                <p class="mb-0 fw-medium">
                  <a href="{{url('front-pages/help-center-article')}}">29 articles</a>
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge bg-label-danger p-2 rounded me-2"><i class="bx bx-world bx-sm"></i></span>
                  <h5 class="fw-medium mt-3 ms-1">Domains and Email</h5>
                </div>
                <ul>
                  <li class="text-primary py-1">
                    <a href="{{url('front-pages/help-center-article')}}">Access to Admin Account</a>
                  </li>
                  <li class="text-primary pb-1">
                    <a href="{{url('front-pages/help-center-article')}}">Send Email From an Alias</a>
                  </li>
                </ul>
                <p class="mb-0 fw-medium">
                  <a href="{{url('front-pages/help-center-article')}}">22 articles</a>
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge bg-label-warning p-2 rounded me-2"><i class="bx bx-mobile-alt bx-sm"></i></span>
                  <h5 class="fw-medium mt-3 ms-1">Mobile Apps</h5>
                </div>
                <ul>
                  <li class="text-primary py-1">
                    <a href="{{url('front-pages/help-center-article')}}">Getting Started with the App</a>
                  </li>
                  <li class="text-primary pb-1">
                    <a href="{{url('front-pages/help-center-article')}}">Getting Started with Android</a>
                  </li>
                </ul>
                <p class="mb-0 fw-medium">
                  <a href="{{url('front-pages/help-center-article')}}">24 articles</a>
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-4 col-sm-6 mb-4">
            <div class="card">
              <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                  <span class="badge bg-label-secondary p-2 rounded me-2"><i class="bx bx-envelope bx-sm"></i></span>
                  <h5 class="fw-medium mt-3 ms-1">Email Marketing</h5>
                </div>
                <ul>
                  <li class="text-primary py-1"><a href="{{url('front-pages/help-center-article')}}">Getting Started</a></li>
                  <li class="text-primary pb-1">
                    <a href="{{url('front-pages/help-center-article')}}">How does this work?</a>
                  </li>
                </ul>
                <p class="mb-0 fw-medium">
                  <a href="{{url('front-pages/help-center-article')}}">27 articles</a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Knowledge Base -->

<!-- Keep Learning -->
<section class="section-py help-center-keep-learning">
  <div class="container">
    <h4 class="text-center pb-4">Keep Learning</h4>
    <div class="row">
      <div class="col-lg-10 mx-auto">
        <div class="row">
          <div class="col-md-4 mb-md-0 mb-4 text-center">
            <img src="{{asset('assets/img/icons/unicons/chart.png')}}" class="mb-2" height="48" alt="Help center blog">
            <h5 class="my-3">Blogging</h5>
            <p class="mb-1"> Expert tips and tools to improve your website or online store using our blog. </p>
            <a href="{{url('front-pages/help-center-article')}}" class="d-flex align-items-center justify-content-center mt-2">
              <span class="align-middle me-1">Learn More</span>
              <i class="bx bx-right-arrow-circle scaleX-n1-rtl"></i>
            </a>
          </div>
          <div class="col-md-4 mb-md-0 mb-4 text-center">
            <img src="{{asset('assets/img/icons/unicons/cc-warning.png')}}" class="mb-2" height="48" alt="Help center inspiration">
            <h5 class="my-3">Inspiration Center</h5>
            <p class="mb-1"> Inspiration from experts to help you start and grow your big ideas. </p>
            <a href="{{url('front-pages/help-center-article')}}" class="d-flex align-items-center justify-content-center mt-2">
              <span class="align-middle me-1">Learn More</span>
              <i class="bx bx-right-arrow-circle scaleX-n1-rtl"></i></a>
          </div>
          <div class="col-md-4 text-center">
            <img src="{{asset('assets/img/icons/unicons/community.png')}}" class="mb-2" height="48" alt="Help center inspiration">
            <h5 class="my-3">Community</h5>
            <p class="mb-1"> A group of people living in the same place or having a particular. </p>
            <a href="{{url('front-pages/help-center-article')}}" class="d-flex align-items-center justify-content-center mt-2">
              <span class="align-middle me-1">Learn More</span>
              <i class="bx bx-right-arrow-circle scaleX-n1-rtl"></i></a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Keep Learning -->

<!-- Help Area -->
<section class="section-py bg-body help-center-contact-us help-center-bg-alt">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-8 col-lg-6 text-center">
        <h4>Still need help?</h4>
        <p class="mb-4">Our specialists are always happy to help. Contact us during standard <br>
          business hours or email us 24/7 and we'll get back to you.</p>
        <div class="d-flex justify-content-center flex-wrap gap-4">
          <a href="javascript:void(0);" class="btn btn-label-primary">Visit our community</a>
          <a href="javascript:void(0);" class="btn btn-label-primary">Contact us</a>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /Help Area -->
@endsection
