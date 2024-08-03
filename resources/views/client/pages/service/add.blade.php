@extends('client.client_template')
    @section('content')

    <style>
        .mb-3{
            margin-left: 15px;
        }
    </style>

    <div class="container-xxl flex-grow-1 container-p-y">
        
        <h4 class="py-3 breadcrumb-wrapper mb-4">
            <span class="text-muted fw-light">Services /</span> Add 
        </h4>

        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Service Info.</h5>
                </div>
                <div class="card-body">
                    <form>
                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Service Name</label>
                        <input type="text" class="form-control" id="basic-default-fullname" placeholder="Service Name" />
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="basic-default-fullname">Full Editor</label>
                        <div id="full-editor">
                            <label class="form-label" for="basic-default-fullname">Quill Rich Text Editor</label>
                            <p>
                            Cupcake ipsum dolor sit amet. Halvah cheesecake chocolate bar gummi bears cupcake. Pie
                            macaroon bear claw. Soufflé I love candy canes I love cotton candy I love.
                            </p>
                        </div>
                    </div>

                    <div class="">
                        <h5>Orders of this service</h5>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="deliveryAdd">
                        <label class="form-check-label" for="deliveryAdd">
                            This is an add-on, don't create a new order
                        </label>
                        </div>

                        <div class="mb-2">
                            Intake form will be appended to selected services when purchased together.
                        </div>

                        <div class="col-md-6 mb-4 mb-md-0">
                          <div class="select2-dark">
                            <select id="parent_services" class="select2 form-select" multiple>
                              <option value="1">Option1</option>
                              <option value="2">Option2</option>
                              <option value="3">Option3</option>
                              <option value="4">Option4</option>
                            </select>
                          </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="deliveryAdd">
                        <label class="form-check-label" for="deliveryAdd">
                            Group multiple quantities of this service into one order
                        </label>
                        </div>
                        <div class="mb-2">
                            By default purchases of multiple quantities are added as separate orders. Different services are always added separately.
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="deliveryAdd">
                        <label class="form-check-label" for="deliveryAdd">
                            Assign to a team member
                        </label>
                        </div>
                        <div class="mb-2">
                            Automatically assign orders of this service to a team member.
                        </div>    
                        
                        <div class="col-md-6 mb-4 mb-md-0">
                          <div class="select2-dark">
                            <select id="select_team" class="select2 form-select" multiple>
                              <option value="1">Option1</option>
                              <option value="2">Option2</option>
                              <option value="3">Option3</option>
                              <option value="4">Option4</option>
                            </select>
                          </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="deliveryAdd">
                        <label class="form-check-label" for="deliveryAdd">
                            Set a deadline
                        </label>
                        </div>
                        <div class="mb-2">
                            Helps your team see orders that are due soon, not visible to clients.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Subnit</button>
                    </form>
                </div>
                </div>
            </div>
    </div>
</div>
@endsection
