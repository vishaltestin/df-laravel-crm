@extends('FrontEnd.layouts.master')
@section('title', 'Organizations')
@section('css')
<link href="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">

@endsection
@section('css')
@endsection
@section('content')
    @component('FrontEnd.components.breadcrumb')
        @slot('li_1')
            CRM
        @endslot
        @slot('title')
            Organizations
        @endslot
    @endcomponent

    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="leadsList">
                <div class="card-header border-0">

                    <div class="row g-4 align-items-center">
                        <div class="col-sm-3">
                            <div class="search-box">
                                <input type="text" class="form-control search"
                                    placeholder="Search for...">
                                <i class="ri-search-line search-icon"></i>
                            </div>
                        </div>
     


            <div class="col-sm-auto ms-auto">
                            <div class="hstack gap-2">
                                                       	                @if(in_array($currentRoleSlug, ['root','sadmin']))
     <button type="button" class="btn btn-success add-btn"
                                    data-bs-toggle="modal" id="create-btn"
                                    data-bs-target="#showAddModal"><i
                                        class="ri-add-line align-bottom me-1"></i> Add Organizations</button>
                             <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
@endif


 @if(in_array($currentRoleSlug, ['admin']))
  <!--   <button type="button" class="btn btn-success add-btn"
                                    data-bs-toggle="modal" id="create-btn"
                                    data-bs-target="#showAddModaladmin"><i
                                        class="ri-add-line align-bottom me-1"></i> Add Organizations</button>
 
   
<div class="modal fade" id="showAddModaladmin" tabindex="-1" aria-labelledby="addorgModelLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
                                    <h5 class="modal-title" id="addorgModelLabel">Add Organization</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"   aria-label="Close" id="close-modal"></button>
                                </div>
                                <form class="tablelist-form"  id="addOrganizationForm"  autocomplete="off" enctype="multipart/form-data">
                                  @csrf  
								  <div class="modal-body">
                                        <input type="hidden" id="id-field" />
                                        <div class="row g-3">
                                            <div class="col-lg-12">
                                                <div class="text-center">
                                                    <div class="position-relative d-inline-block">
                                                        <div class="position-absolute bottom-0 end-0">
                                                            <label for="lead-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Select Image">
                                                                <div class="avatar-xs cursor-pointer">
                                                                    <div class="avatar-title bg-light border rounded-circle text-muted">
                                                                        <i class="ri-image-fill"></i>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <input class="form-control d-none"  id="lead-image-input" type="file" accept="image/png, image/gif, image/jpeg" name="logo">
                                                        </div>
                                                        <div class="avatar-lg p-1">
                                                            <div class="avatar-title bg-light rounded-circle">
                                                                <img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}" alt="" id="lead-img" class="avatar-md rounded-circle object-fit-cover" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-13 mt-3">Logo </h5>
                                                </div>
                                                <div>
                                                    <label for="Name-field"      class="form-label">Organization Name*</label>
                                                    <input type="text" id="Name-field" class="form-control" placeholder="Enter Name"  required name="name">
                                                </div>
                                            </div>
											
                                          
			 
                       
                                            <div class="col-lg-12">
                                                <div>
                                                    <label for="Address-field"   class="form-label">Address*</label>
                                                    <input type="text" id="Address-field"  class="form-control"  placeholder="Enter Address" name="address" required />
                                                </div>
                                            </div>
                                             <div class="col-lg-6">
                                                <div>
                                                    <label for="City-field"    class="form-label">City*</label>
                                                    <input type="text" id="City-field" class="form-control" placeholder="Enter City" name="city" required />
                                                </div>
                                            </div>
                                             <div class="col-lg-6">
                                                <div>
                                                    <label for="Country" class="form-label">Country*</label>
                                                    <select class="form-control" name="country" required  id="Country"   >
                                                      <option value="">Select Country</option>
													  @foreach($countries as $country)
														<option value="{{ $country->name }}">{{ $country->name }}</option>
														@endforeach	
                                                    </select>
                                                </div>
                                            </div>
											<div class="col-lg-6">
                                                <div>
                                                    <label for="State" class="form-label">State*</label>
                                                    <input type="text" id="State"    class="form-control"  placeholder="Enter State" required  name="state">
                                                </div>
                                            </div>
 <div class="col-lg-6">
                                                <div>
                                                    <label for="Zip-field" class="form-label">Zip Code </label>
                                                    <input type="text" id="Zip-field" class="form-control"  placeholder="Enter Zipcode" name="zipcode" required />
                                                </div>
                                            </div>
											
											 
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="Status-field" class="form-label">Status </label>
                                                    <select class="form-control" name="status"  required id="Status-field">
  													<option value="">Select Status</option>
 													<option value="1">Active</option>
														<option value="0">Deactive</option>
													</select>

												</div>
                                            </div>
											
											
											
											
											
											<div class="showerror"></div>
											
											
											
                                         </div>
                                     </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success"
                                                id="add-btn">Add Organization</button>
                                            {{-- <button type="button" class="btn btn-success"
                                                id="edit-btn">Update</button> --}}
                </div>

                  </div>
  </form>

        </div>
    </div>
</div>

                     <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-labelledby="deleteRecordLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close" id="btn-close"></button>
                                </div>
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                        style="width:90px;height:90px"></lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4 class="fs-semibold">You are about to delete a organization?</h4>
                                        <p class="text-muted fs-14 mb-4 pt-1">Deleting your organization will
                                            remove all of your information from our database.</p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button
                                                class="btn btn-link link-success fw-medium text-decoration-none"
                                                data-bs-dismiss="modal" id="deleteRecord-close"><i
                                                    class="ri-close-line me-1 align-middle"></i>
                                                Close</button>
                                            <button class="btn btn-danger" id="delete-record">Yes,
                                                Delete It!!</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
  
                
 <div class="modal fade" id="showeditModal" tabindex="-1" aria-labelledby="editorgModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
                                    <h5 class="modal-title" id="editorgModelLabel">Edit Organization</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"   aria-label="Close" id="close-modal"></button>
                                </div>
                                <form class="tablelist-form"  id="editOrganizationForm"  autocomplete="off" enctype="multipart/form-data">
                                  @csrf    <div class="modal-body">
                                        <input type="hidden" id="id-field-edit" />
                                        <div class="row g-3">
                                            <div class="col-lg-12">
                                                <div class="text-center">
                                                    <div class="position-relative d-inline-block">
                                                        <div class="position-absolute bottom-0 end-0">
                                                            <label for="lead-image-input-edit" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Select Image">
                                                                <div class="avatar-xs cursor-pointer">
                                                                    <div class="avatar-title bg-light border rounded-circle text-muted">
                                                                        <i class="ri-image-fill"></i>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <input class="form-control d-none"  id="lead-image-input-edit" type="file" accept="image/png, image/gif, image/jpeg" name="logo">
                                                        </div>
                                                        <div class="avatar-lg p-1">
                                                            <div class="avatar-title bg-light rounded-circle">
                                                                <img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}" alt="" id="lead-img-edit" class="avatar-md rounded-circle object-fit-cover" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-13 mt-3">Logo </h5>
                                                </div>
                                                <div>
                                                    <label for="Name-field"      class="form-label">Organization Name*</label>
                                                    <input type="text" id="Name-field-edit" class="form-control" placeholder="Enter Name"  required name="name">
                                                </div>
                                            </div>
                                         
                                             <div class="col-lg-12">
                                                <div>
                                                    <label for="Address-field"   class="form-label">Address*</label>
                                                    <input type="text" id="Address-field-edit"  class="form-control"  placeholder="Enter Address" name="address" required />
                                                </div>
                                            </div>
                                             <div class="col-lg-6">
                                                <div>
                                                    <label for="City-field"    class="form-label">City*</label>
                                                    <input type="text" id="City-field-edit" class="form-control" placeholder="Enter City" name="city" required />
                                                </div>
                                            </div>
                                             <div class="col-lg-6">
                                                <div>
                                                    <label for="Country" class="form-label">Country*</label>
                                                    <select class="form-control" name="country" required  id="Country-edit"   >
                                                      <option value="">Select Country</option>
													  @foreach($countries as $country)
														<option value="{{ $country->name }}">{{ $country->name }}</option>
														@endforeach	
                                                    </select>
                                                </div>
                                            </div>
											<div class="col-lg-6">
                                                <div>
                                                    <label for="State" class="form-label">State*</label>
                                                    <input type="text" id="State-edit"    class="form-control"  placeholder="Enter State" required  name="state">
                                                </div>
                                            </div>
 <div class="col-lg-6">
                                                <div>
                                                    <label for="Zip-field" class="form-label">Zip Code </label>
                                                    <input type="text" id="Zip-field-edit" class="form-control"  placeholder="Enter Zipcode" name="zipcode" required />
                                                </div>
                                            </div>
											
											 
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="Status-field" class="form-label">Status </label>
                                                    <select class="form-control" name="status"  required id="Status-field-edit">
  													<option value="">Select Status</option>
                                                     														<option value="0">Deactive</option>
													<option value="1">Active</option>
														<option value="0">Deactive</option>
													</select>

												</div>
                                            </div>
											
											
											
											
											
											<div class="showerror1"></div>
											
											
											
                                         </div>
                             <input type="hidden" name="_method" value="PUT">        </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success"
                                                id="add-btn">Edit Organization</button>
                                            {{-- <button type="button" class="btn btn-success"
                                                id="edit-btn">Update</button> --}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
 -->
@endif

	
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div>
                        <div class="table-responsive table-card">
                            <table class="table align-middle" id="customerTable">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col" style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="checkAll" value="option">
                                            </div>
                                        </th>

                                        <th class="sort" data-sort="name">Name</th>
										<th class="sort" data-sort="address">Address</th>
                                        <th class="sort" data-sort="country">Country</th>
                                         <th class="sort" data-sort="date">Create Date</th>
                                        <th class="sort" data-sort="action">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
								@foreach($organizations  as $org)

                                    <tr>
                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    name="chk_child" value="option1">
                                            </div>
                                        </th>
                                        <td class="id" style="display:none;"><a
                                                href="javascript:void(0);"
                                                class="fw-medium link-primary">#VZ2101</a></td>
                                        <td class="name">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                               
											   @if(!empty($org->logo) && file_exists(public_path($org->logo)))
													<img src="{{ URL::asset($org->logo) }}" alt="Logo"  alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">
												@else
													<img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}" alt="Default Logo"  alt="" class="avatar-xxs rounded-circle image_src object-fit-cover">
												@endif 
                                                </div>
                                                <div class="flex-grow-1 ms-2 name">{{ $org->name }}</div>
                                            </div>
                                        </td>
                                        
                                        <td class="address">{{ $org->address }} {{ $org->city }} {{ $org->state }} {{ $org->zipcode }}</td>
                                        <td class="country">{{ $org->country }}</td>
                                        
                                        <td class="date">{{ $org->created_at }}</td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0">
                                                
                                                
                                                <li class="list-inline-item" data-bs-toggle="tooltip"
                                                    data-bs-trigger="hover" data-bs-placement="top"
                                                    title="View">
                                                    <a href="organizations/{{ encrypt($org->id) }}"><i
                                                            class="ri-eye-fill align-bottom text-muted"></i></a>
                                                </li>
                     @if(
    in_array($currentRoleSlug, ['root','sadmin']) 
    || ($org->created_by == optional(\Sentinel::check())->id)
)

<li class="list-inline-item" data-bs-toggle="tooltip"
    data-bs-trigger="hover" data-bs-placement="top"
    title="Edit">
    <a class="edit-item-btn editOrg" href="#edit" data-id="{{ $org->id }}">
        <i class="ri-pencil-fill align-bottom text-muted"></i>
    </a>
</li>

<li class="list-inline-item" data-bs-toggle="tooltip"
    data-bs-trigger="hover" data-bs-placement="top"
    title="Delete">
    <a class="remove-item-btn" data-bs-toggle="modal"
        href="#deleteRecordModal">
        <i class="ri-delete-bin-fill align-bottom text-muted"></i>
    </a>
</li>

@endif
                                            </ul>
                                        </td>
                                    </tr>
                               @endforeach

 </tbody>
                            </table>
                            <div class="noresult" style="display: none">
                                <div class="text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                        trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                        style="width:75px;height:75px">
                                    </lord-icon>
                                    <h5 class="mt-2">Sorry! No Result Found</h5>
                                    <p class="text-muted mb-0">We've searched more than 150+ leads We
                                        did not find any
                                        leads for you search.</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <div class="pagination-wrap hstack gap-2">
                                <a class="page-item pagination-prev disabled" href="#">
                                    Previous
                                </a>
                                <ul class="pagination listjs-pagination mb-0"></ul>
                                <a class="page-item pagination-next" href="#">
                                    Next
                                </a>
                            </div>
                        </div>
                    </div>
					
	 @if(in_array($currentRoleSlug, ['root','sadmin']))

                    <div class="modal fade" id="showAddModal" tabindex="-1"   aria-labelledby="addorgModelLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
                                    <h5 class="modal-title" id="addorgModelLabel">Add Organization</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"   aria-label="Close" id="close-modal"></button>
                                </div>
                                <form class="tablelist-form"  id="addOrganizationForm"  autocomplete="off" enctype="multipart/form-data">
                                  @csrf    <div class="modal-body">
                                        <input type="hidden" id="id-field" />
                                        <div class="row g-3">
                                            <div class="col-lg-12">
                                                <div class="text-center">
                                                    <div class="position-relative d-inline-block">
                                                        <div class="position-absolute bottom-0 end-0">
                                                            <label for="lead-image-input" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Select Image">
                                                                <div class="avatar-xs cursor-pointer">
                                                                    <div class="avatar-title bg-light border rounded-circle text-muted">
                                                                        <i class="ri-image-fill"></i>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <input class="form-control d-none"  id="lead-image-input" type="file" accept="image/png, image/gif, image/jpeg" name="logo">
                                                        </div>
                                                        <div class="avatar-lg p-1">
                                                            <div class="avatar-title bg-light rounded-circle">
                                                                <img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}" alt="" id="lead-img" class="avatar-md rounded-circle object-fit-cover" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-13 mt-3">Logo </h5>
                                                </div>
                                                <div>
                                                    <label for="Name-field"      class="form-label">Organization Name*</label>
                                                    <input type="text" id="Name-field" class="form-control" placeholder="Enter Name"  required name="name">
                                                </div>
                                            </div>
											
                                          
											
									<!-- Contact Person First / Last Name -->
<div class="row g-3 mt-2">
    <div class="col-lg-6">
        <label for="OrgFirstName" class="form-label">Contact First Name*</label>
        <input type="text"
               id="OrgFirstName"
               class="form-control"
               placeholder="Enter First Name"
               name="first_name"
               required>
    </div>

    <div class="col-lg-6">
        <label for="OrgLastName" class="form-label">Contact Last Name*</label>
        <input type="text"
               id="OrgLastName"
               class="form-control"
               placeholder="Enter Last Name"
               name="last_name"
               required>
    </div>
</div>		
				  <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="Email-field"
                                                        class="form-label">Email*</label>
                                                    <input type="text" id="Email-field"  class="form-control" placeholder="Enter email"    name="email" required />
                                                </div>
                                            </div>							
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="lMobile-field"   class="form-label">Mobile*</label>
                                                    <input type="text" id="Mobile-field" class="form-control"  placeholder="Enter mobile"  name="mobile" required />
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div>
                                                    <label for="Address-field"   class="form-label">Address*</label>
                                                    <input type="text" id="Address-field"  class="form-control"  placeholder="Enter Address" name="address" required />
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="City-field"    class="form-label">City*</label>
                                                    <input type="text" id="City-field" class="form-control" placeholder="Enter City" name="city" required />
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="Country" class="form-label">Country*</label>
                                                    <select class="form-control" name="country" required  id="Country"   >
                                                      <option value="">Select Country</option>
													  @foreach($countries as $country)
														<option value="{{ $country->name }}">{{ $country->name }}</option>
														@endforeach	
                                                    </select>
                                                </div>
                                            </div>
											<div class="col-lg-6">
                                                <div>
                                                    <label for="State" class="form-label">State*</label>
                                                    <input type="text" id="State"    class="form-control"  placeholder="Enter State" required  name="state">
                                                </div>
                                            </div>
 <div class="col-lg-6">
                                                <div>
                                                    <label for="Zip-field" class="form-label">Zip Code </label>
                                                    <input type="text" id="Zip-field" class="form-control"  placeholder="Enter Zipcode" name="zipcode" required />
                                                </div>
                                            </div>
											
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="GST-field" class="form-label">GST </label>
                                                    <input type="text" id="GST-field" class="form-control"   placeholder="Enter GST" name="gst"    >
                                                </div>
                                            </div>
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="Status-field" class="form-label">Status </label>
                                                    <select class="form-control" name="status"  required id="Status-field">
  													<option value="">Select Status</option>
 													<option value="1">Active</option>
														<option value="0">Deactive</option>
													</select>

												</div>
                                            </div>
											
											
											
											
											
											<div class="showerror"></div>
											
											
											
                                         </div>
                                        <!--end row-->
                                    </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success"
                                                id="add-btn">Add Organization</button>
                                            {{-- <button type="button" class="btn btn-success"
                                                id="edit-btn">Update</button> --}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end modal-->

                    <!-- Modal -->
                    <div class="modal fade zoomIn" id="deleteRecordModal" tabindex="-1" aria-labelledby="deleteRecordLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close" id="btn-close"></button>
                                </div>
                                <div class="modal-body p-5 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json"
                                        trigger="loop" colors="primary:#405189,secondary:#f06548"
                                        style="width:90px;height:90px"></lord-icon>
                                    <div class="mt-4 text-center">
                                        <h4 class="fs-semibold">You are about to delete a organization?</h4>
                                        <p class="text-muted fs-14 mb-4 pt-1">Deleting your organization will
                                            remove all of your information from our database.</p>
                                        <div class="hstack gap-2 justify-content-center remove">
                                            <button
                                                class="btn btn-link link-success fw-medium text-decoration-none"
                                                data-bs-dismiss="modal" id="deleteRecord-close"><i
                                                    class="ri-close-line me-1 align-middle"></i>
                                                Close</button>
                                            <button class="btn btn-danger" id="delete-record">Yes,
                                                Delete It!!</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end modal -->


                    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasExample"   aria-labelledby="offcanvasExampleLabel">
                        <div class="offcanvas-header bg-light">
                            <h5 class="offcanvas-title" id="offcanvasExampleLabel">Organizations Fliters</h5>
                            <button type="button" class="btn-close text-reset"
                                data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        </div>
                        <!--end offcanvas-header-->
                        <form action="" class="d-flex flex-column justify-content-end h-100">
                            <div class="offcanvas-body">
                                <div class="mb-4">
                                    <label for="datepicker-range"
                                        class="form-label text-muted text-uppercase fw-semibold mb-3">Create Date</label>
                                    <input type="date" class="form-control" id="datepicker-range"    data-provider="flatpickr" data-range="true" placeholder="Select date">
                                </div>
                                <div class="mb-4">
                                    <label for="country-select"
                                        class="form-label text-muted text-uppercase fw-semibold mb-3">Country</label>
                                    <select class="form-control" data-choices
                                        data-choices-multiple-remove="true" name="country-select"  id="country-select" multiple>
                                        <option value="">Select Country</option>
													  @foreach($countries as $country)
														<option value="{{ $country->name }}">{{ $country->name }}</option>
														@endforeach	
                                    </select>
                                </div>
                                <div class="mb-4">
                                    <label for="status-select"
                                        class="form-label text-muted text-uppercase fw-semibold mb-3">Status</label>
                                    <div class="row g-2">
                                        <div class="col-lg-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="inlineCheckbox1" value="option1">
                                                <label class="form-check-label"
                                                    for="inlineCheckbox1">New Organizations</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="inlineCheckbox2" value="option2">
                                                <label class="form-check-label"
                                                    for="inlineCheckbox2">Old Organizations</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="inlineCheckbox3" value="option3">
                                                <label class="form-check-label"
                                                    for="inlineCheckbox3">Active Organizations</label>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="inlineCheckbox4" value="option4">
                                                <label class="form-check-label"
                                                    for="inlineCheckbox4">Off Organizations</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>    <div>
                                 </div>
                            </div>
                            <!--end offcanvas-body-->
                            <div class="offcanvas-footer border-top p-3 text-center hstack gap-2">
                                <button class="btn btn-light w-100">Clear Filter</button>
                                <button type="submit" class="btn btn-success w-100">Filters</button>
                            </div>
                            <!--end offcanvas-footer-->
                        </form>
                    </div>
                    <!--end offcanvas-->


<!--editfoem-->
<div class="modal fade" id="showeditModal" tabindex="-1" aria-labelledby="editorgModelLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">

                            <div class="modal-content">
                                <div class="modal-header bg-light p-3">
                                    <h5 class="modal-title" id="editorgModelLabel">Edit Organization</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"   aria-label="Close" id="close-modal"></button>
                                </div>
                                <form class="tablelist-form"  id="editOrganizationForm"  autocomplete="off" enctype="multipart/form-data">
                                  @csrf    <div class="modal-body">
                                        <input type="hidden" id="id-field-edit" />
                                        <div class="row g-3">
                                            <div class="col-lg-12">
                                                <div class="text-center">
                                                    <div class="position-relative d-inline-block">
                                                        <div class="position-absolute bottom-0 end-0">
                                                            <label for="lead-image-input-edit" class="mb-0" data-bs-toggle="tooltip" data-bs-placement="right" title="Select Image">
                                                                <div class="avatar-xs cursor-pointer">
                                                                    <div class="avatar-title bg-light border rounded-circle text-muted">
                                                                        <i class="ri-image-fill"></i>
                                                                    </div>
                                                                </div>
                                                            </label>
                                                            <input class="form-control d-none"  id="lead-image-input-edit" type="file" accept="image/png, image/gif, image/jpeg" name="logo">
                                                        </div>
                                                        <div class="avatar-lg p-1">
                                                            <div class="avatar-title bg-light rounded-circle">
                                                                <img src="{{ URL::asset('build/images/users/user-dummy-img.jpg') }}" alt="" id="lead-img-edit" class="avatar-md rounded-circle object-fit-cover" >
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <h5 class="fs-13 mt-3">Logo </h5>
                                                </div>
                                                <div>
                                                    <label for="Name-field"      class="form-label">Organization Name*</label>
                                                    <input type="text" id="Name-field-edit" class="form-control" placeholder="Enter Name"  required name="name">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="Email-field"
                                                        class="form-label">Email*</label>
                                                    <input type="text" id="Email-field-edit"  class="form-control" placeholder="Enter email"  name="email" required />
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="lMobile-field"   class="form-label">Mobile*</label>
                                                    <input type="text" id="Mobile-field-edit" class="form-control"  placeholder="Enter mobile"  name="mobile" required />
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div>
                                                    <label for="Address-field"   class="form-label">Address*</label>
                                                    <input type="text" id="Address-field-edit"  class="form-control"  placeholder="Enter Address" name="address" required />
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="City-field"    class="form-label">City*</label>
                                                    <input type="text" id="City-field-edit" class="form-control" placeholder="Enter City" name="city" required />
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div>
                                                    <label for="Country" class="form-label">Country*</label>
                                                    <select class="form-control" name="country" required  id="Country-edit"   >
                                                      <option value="">Select Country</option>
													  @foreach($countries as $country)
														<option value="{{ $country->name }}">{{ $country->name }}</option>
														@endforeach	
                                                    </select>
                                                </div>
                                            </div>
											<div class="col-lg-6">
                                                <div>
                                                    <label for="State" class="form-label">State*</label>
                                                    <input type="text" id="State-edit"    class="form-control"  placeholder="Enter State" required  name="state">
                                                </div>
                                            </div>
 <div class="col-lg-6">
                                                <div>
                                                    <label for="Zip-field" class="form-label">Zip Code </label>
                                                    <input type="text" id="Zip-field-edit" class="form-control"  placeholder="Enter Zipcode" name="zipcode" required />
                                                </div>
                                            </div>
											
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="GST-field" class="form-label">GST </label>
                                                    <input type="text" id="GST-field-edit" class="form-control"   placeholder="Enter GST" name="gst"    >
                                                </div>
                                            </div>
											 <div class="col-lg-6">
                                                <div>
                                                    <label for="Status-field" class="form-label">Status </label>
                                                    <select class="form-control" name="status"  required id="Status-field-edit">
  													<option value="">Select Status</option>
                                                     														<option value="0">Deactive</option>
													<option value="1">Active</option>
														<option value="0">Deactive</option>
													</select>

												</div>
                                            </div>
											
											
											
											
											
											<div class="showerror1"></div>
											
											
											
                                         </div>
                                        <!--end row-->
                            <input type="hidden" name="_method" value="PUT">        </div>
                                    <div class="modal-footer">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-success"
                                                id="add-btn">Edit Organization</button>
                                            {{-- <button type="button" class="btn btn-success"
                                                id="edit-btn">Update</button> --}}
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!--end modal-->

@endif




                </div>
            </div>

        </div>
        <!--end col-->
    </div>
    <!--end row-->

@endsection
@section('script')
    <script src="{{ URL::asset('build/libs/list.js/list.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/list.pagination.js/list.pagination.min.js') }}"></script>
    <script src="{{ URL::asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
 	 		<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="{{ URL::asset('build/js/pages/crm-leads.init.js') }}"></script>

    <!-- App js -->
    	

	
<script>
console.log("jQuery version:", $.fn.jquery);
</script>
	 @if(in_array($currentRoleSlug, ['root','sadmin','admin']))

<script>
$(document).ready(function() {

    // ✅ alert अब चलेगा
 
    // -------------------------------
    // 🔹 Add Organization Form Submit
    // -------------------------------
    $('#addOrganizationForm').on('submit', function(e) {
        e.preventDefault();

        var formData = new FormData(this);
        $.ajax({
            url: "{{ route('Organization.Store') }}",
            method: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if(response.status === 'success') {
                    $('#showAddModal').modal('hide');
                    $('#addOrganizationForm')[0].reset();
                    $('#organizationTable tbody').append(); // ⚠️ यह खाली append() है
                        location.reload();
        } else {
                    $('.showerror').html('<div class="alert alert-danger">Some fields are missing.</div>');
                }
            },
            error: function(xhr) {
                $('.showerror').html('<div class="alert alert-danger">' + xhr.responseText + '</div>');
            }
        });
    });

    // -------------------------------
    // 🔹 Edit Organization Button Click
    // -------------------------------
    $(document).on('click', '.editOrg', function() {
         var orgId = $(this).data('id');
 
        $('#showeditModal').modal('show');
        $('.showerror').html('');
$('#Email-field-edit').prop('readonly', true);
        $.ajax({
            url: '{{ url('organizations') }}/' + orgId + '/edit',
            type: 'GET',
            success: function(response) {
                $('#id-field-edit').val(response.id);
                $('#Name-field-edit').val(response.name);
                $('#Email-field-edit').val(response.email);
                $('#Mobile-field-edit').val(response.mobile);
                $('#Address-field-edit').val(response.address);
                $('#City-field-edit').val(response.city);
                $('#Country-edit').val(response.country);
                $('#State-edit').val(response.state);
                $('#Zip-field-edit').val(response.zipcode);
                $('#GST-field-edit').val(response.gst);
                $('#Status-field-edit').val(response.status);
				$("#Status-field-edit").val(response.status).change();

                if(response.logo){
                    $("#lead-img-edit").attr("src",'{{ URL::asset('') }}' + response.logo);
                } else {
                    $('#lead-img-edit').attr('src', '{{ URL::asset("build/images/users/user-dummy-img.jpg") }}');
                }
            }
        });
    });

    // -------------------------------
    // 🔹 Update Organization Form Submit
    // -------------------------------
    $('#editOrganizationForm').submit(function(e) {
        e.preventDefault();
        var id = $('#id-field-edit').val();
        var formData = new FormData($('#editOrganizationForm')[0]);

        $.ajax({
            url: '{{ url('organizations') }}/' + id,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(response) {
                $('#showeditModal').modal('hide');
                alert('Organization updated successfully!');
                location.reload();
            },
            error: function(xhr) {
                var errors = xhr.responseJSON.errors;
                var errorList = '';
                $.each(errors, function(key, val) {
                    errorList += '<div class="alert alert-danger">'+ val[0] +'</div>';
                });
                $('.showerror1').html(errorList);
            }
        });
    });
});
</script>

@endif

@endsection
