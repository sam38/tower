<form action="policies" class="card validate-form shadow" method="post">
    <div class="card-header">Insurance Policy</div>
    <div class="card-body">
        <div class="form-group">
            <label for="policyName">Policy Name<i class="text-danger">*</i></label>
            <input 
                type="text" 
                class="form-control" 
                id="policyName" 
                name="title" 
                placeholder="Enter policy name"
                maxlength="250"
                data-validate="required"
                value="test"
                required
            />
        </div>
        <div class="form-group">
            <label for="policyId">Policy ID<i class="text-danger">*</i></label>
            <input 
                type="integer" 
                class="form-control" 
                id="policyId" 
                name="policy-id"
                placeholder="Enter your policy ID (number)"
                maxlength="30"
                min="1"
                value="1"
                data-validate="required|number"
                required
            />
        </div>
        <div class="form-group">
            <label for="policyDate">Live Date<i class="text-danger">*</i></label>
            <input 
                id="policyDate"
                name="policy-date"
                type="text" 
                class="form-control" 
                placeholder="Enter policy date" 
                aria-label="policyDateHelp" 
                aria-describedby="basic-addon1"
                maxlength="10"
                data-validate="required|date"
                value="08/06/2021"
                required
            />
            <small id="policyDateHelp" class="form-text text-muted"
            >Enter date in format DD/MM/YYY. e.g. <?php echo date('d/m/Y') ?></small>
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <textarea 
                class="form-control" 
                id="description" 
                name="policy-description" 
                rows="5"
                maxlength="1200"
            >test</textarea>
        </div>
        <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary mt-3">Submit</button>
            <small class="float-right text-muted font-italic">
                <i class="text-danger">*</i> Marked as required
            </small>
        </div>
    </div>
</form>
