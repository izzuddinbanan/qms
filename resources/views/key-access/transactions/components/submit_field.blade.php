<div id="submit_field" class="transcation_field">
                            
    <div class="row">
        <div class="col-md-9 col-md-offset-1">

            <div class="col-md-12">
                <label>List of Items:</label>
            </div>

            <!-- <div class="col-md-2"></div> -->
            <div class="col-md-12">
                <table class="table table-xxs"  id="myTable">
                    <thead>
                        <tr>
                            <td class="" >No.</td>
                            <td class="" >Code</td>
                            <td class="" >Name</td>
                            <td class="" >Quantity</td>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td><input type="text" name="code[]" value="" class="form-control" placeholder="e.g 1001" autocomplete="off"></td>
                            <td><input type="text" name="name[]" value="" class="form-control" placeholder="e.g Master Room" autocomplete="off" required=""></td>
                            <td><input type="number" name="quantity[]" value="1" class="form-control" placeholder="e.g 4" min="1" required=""></td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="4"><button class="btn btn-primary btn-xs" id="add-item" type="button">Add Item</button></td>
                        </tr>
                    </tfoot>
                </table>

            </div>
        </div>
    </div>
</div>