 <!-- Start footer -->
        <footer class="custom-footer bg-dark py-5 position-relative">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 mt-4">
                        <div>
                            <div>
                                <img src="assets/images/logo-light.png" alt="logo light" height="17">
                            </div>
                            <div class="mt-4 fs-13">
                                <p>{{ $content['title'] }}</p>
                                <p class="ff-secondary">{{ $content['subtitle'] }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-7 ms-lg-auto">
                         
							<div class="row mt-4">
								@foreach($content['columns'] as $col)
                            <div class="col-sm-4 mt-4">
									<h5 class="text-white mb-0">{{ $col['heading'] }}</h5>
                                    <ul class="list-unstyled ff-secondary footer-list fs-14">
										@foreach($col['links'] as $link)
										<li><a href="{{ $link['url'] }}">{{ $link['label'] }}</a></li>
										@endforeach
									</ul>
								</div>
								@endforeach
							</div>

                     </div>

                </div>

                <div class="row text-center text-sm-start align-items-center mt-5">
                    <div class="col-sm-6">

                        <div>
                            <p class="copy-rights mb-0">
                                <script> document.write(new Date().getFullYear()) </script> © {{ $content['title'] }} - Powered By DIgital Fueled IT PVt. Ltd.
                            </p>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="text-sm-end mt-3 mt-sm-0">
                             
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end footer -->

        <!--start back-to-top-->
        <button onclick="topFunction()" class="btn btn-danger btn-icon landing-back-top" id="back-to-top">
            <i class="ri-arrow-up-line"></i>
        </button>
		
		
		
		
		
		
		
		
		
		
		
		 