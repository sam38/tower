<form action="tower_policies" class="card validate-form shadow" method="post">
    <div class="card-header">Insurance Policy</div>
    <div class="card-body">
        <div class="form-group">
            <label for="policyName">Policy Name<i class="text-danger">*</i></label>
            <input 
                type="text" 
                class="form-control" 
                id="policyName" 
                placeholder="Enter policy name"
                maxlength="250"
                data-validate="required"
                required
            />
            <!-- <small id="policyNameHelp" class="form-text text-muted"
            >Enter policy name.</small> -->
        </div>
        <div class="form-group">
            <label for="policyId">Policy ID<i class="text-danger">*</i></label>
            <input 
                type="integer" 
                class="form-control" 
                id="policyId" 
                placeholder="Enter your policy ID (number)"
                maxlength="30"
                min="1"
                data-validate="required|number"
            />
        </div>
        <div class="form-group">
            <label for="policyDate">Live Date<i class="text-danger">*</i></label>
            <input 
                id="policyDate"
                type="text" 
                class="form-control" 
                placeholder="Enter policy date" 
                aria-label="policyDateHelp" 
                aria-describedby="basic-addon1"
                maxlength="10"
                data-validate="required|date"
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
                rows="5"
                maxlength="1200"
            ></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
        <small class="float-right text-muted font-italic">
            <i class="text-danger">*</i> Marked as required
        </small>
    </div>
</form>
