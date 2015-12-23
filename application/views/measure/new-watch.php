<div class="container container-fluid content first">
    <div class="row">
        <div class="col-md-12"><center><h1>Add a new watch</h1></center></div>
    </div>
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <form class="form-horizontal" action="/measures/add_watch" method="post" name="addWatch">
                <div class="form-group">
                    <label for="brand" class="col-sm-3 control-label">Brand <i>*</i></label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="brand" placeholder="Jaeger-LeCoultre">
                        <span class="watch-error brand-error">This field is required.</span>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name" class="col-sm-3 control-label">Model</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="name" placeholder="Duometre">
                    </div>
                </div>
                <div class="form-group">
                    <label for="caliber" class="col-sm-3 control-label">Caliber</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="caliber" placeholder="Calibre Jaeger-LeCoultre 381">
                    </div>
                </div>
                <div class="form-group">
                    <label for="yearOfBuy" class="col-sm-3 control-label">Year of buy</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="yearOfBuy" placeholder="1998">
                    </div>
                </div>
                <div class="form-group">
                    <label for="serial" class="col-sm-3 control-label">Serial number</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" name="serial" placeholder="268/53">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-3 col-sm-9">
                        <input type="submit" class="btn btn-primary" name="addWatch" value="Add the watch">
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
