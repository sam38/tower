<form action="claims" class="card validate-form shadow" method="post" novalidate>
    <div class="card-header">Insurance Policy Claim</div>
    <div class="card-body">
        <div class="form-group">
            <label for="policyIdClaim">Policy ID<i class="text-danger">*</i></label>
            <input 
                type="integer" 
                class="form-control" 
                id="policyIdClaim" 
                name="policy-id" 
                placeholder="Enter your policy ID number"
                data-validate="required|number"
                required
                maxlength="30"
                min="1"
            />
        </div>
        <div class="form-group">
            <label for="name">Name<i class="text-danger">*</i></label>
            <input 
                type="text" 
                class="form-control" 
                id="name" 
                name="title" 
                placeholder="Enter your full name."
                data-validate="required"
                maxlength="250"
                required
            />
        </div>
        <div class="form-group">
            <label for="email">Email<i class="text-danger">*</i></label>
            <input 
                type="email" 
                class="form-control" 
                id="email" 
                name="policy-email" 
                placeholder="Enter your email address."
                data-validate="required|email"
                maxlength="250"
                required
            />
        </div>
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
            <small class="float-right text-muted font-italic">
                <i class="text-danger">*</i> Marked as required
            </small>
        </div>
    </div>
</form>