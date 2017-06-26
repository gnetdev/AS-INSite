<div class="entry-header">
    <h2 class="entry-title">Contact Us</h2>
    <p>*All fields are required!</p>
</div>

<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-4">
        <form action="./support/case" method="post" class="form" role="form" id="support-form">
        
            <div class="form-group">
                <label>Name*</label>
                <input class="form-control" name="name" placeholder="Name" type="text" value="<?php echo $this->auth->getIdentity()->fullName(); ?>" required="required" />
            </div>
        
            <div class="form-group">
                <label>Email Address*</label>
                <input class="form-control" name="email" placeholder="Email Address" type="text" value="<?php echo $this->auth->getIdentity()->email; ?>" required="required" />
            </div>
            
            <div class="form-group">
                <label>Phone Number*</label>
                <input class="form-control" name="phone_number" placeholder="Phone Number" type="text" required="required" />
            </div>
                            
            <div class="form-group">
                <label>Subject*</label>
                <input class="form-control" name="subject" placeholder="Subject" type="text" required="required" />
            </div>
            
            <div class="form-group">
                <label>Message*</label>
                <textarea class="form-control" rows="10" name="message" required="required"></textarea>
            </div>
                        
            <button class="btn btn-primary" type="submit">Submit</button>
            
        </form>
    </div>    
</div>

<script>
jQuery(document).ready(function(){
    jQuery('#support-form').one('submit', function(ev){
        ev.preventDefault();

        jQuery(this).submit();
    });
});
</script>