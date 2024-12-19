<!-- Add SweetAlert CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../../css/modal.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.0/dist/sweetalert2.min.js"></script>

<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="paymentModalLabel">
          <i class="fas fa-credit-card mr-2"></i> Process Payment
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="paymentForm">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group">
                <label for="paymentType">
                  <i class="fas fa-wallet mr-2"></i> Payment Type
                </label>
                <select class="form-control" id="paymentType" name="paymentType">
                  <option value="Cash">Cash</option>
                  <option value="Balance">Balance</option>
                </select>
              </div>
              <div class="form-group">
                <label for="amount">
                  <i class="fas fa-file-invoice-dollar mr-2"></i> Total Bill
                </label>
                <input type="number" class="form-control" id="amount" name="amount" >
              </div>
              <div class="form-group">
                <label for="balance">
                  <i class="fas fa-hand-holding-usd mr-2"></i> Balance
                </label>
                <input type="number" class="form-control" id="balance" name="balance" readonly>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-group">
                <label for="totalAmount">
                  <i class="fas fa-calculator mr-2"></i> Total Amount
                </label>
                <input type="number" class="form-control" id="totalAmount" name="totalAmount" readonly>
              </div>
              <div class="form-group">
                <label for="amountPaid">
                  <i class="fas fa-money-bill-wave mr-2"></i> Amount Paid
                </label>
                <input type="number" class="form-control" id="amountPaid" name="amountPaid" required>
              </div>
                            <div class="form-group">
                <label for="multiplier">
                  <i class="fas fa-times-circle mr-2"></i> Multiplier
                </label>
                <input type="number" class="form-control" id="multiplier" name="multiplier" value="1" min="1">
              </div>
              <div class="form-group">
                <label for="change">
                  <i class="fas fa-sync-alt mr-2"></i> Change
                </label>
                <input type="number" class="form-control" id="change" name="change" readonly>
              </div>
              <div class="form-group">
                <label for="addToBalance">
                  <i class="fas fa-arrow-alt-circle-up mr-2"></i> Add Change to Balance?
                </label>
                <select class="form-control" id="addToBalance" name="addToBalance">
                  <option value="yes">Yes</option>
                  <option value="no">No</option>
                  <option value="withdraw">Withdraw</option>
                </select>
              </div>
            </div>
          </div>

          <input type="hidden" id="memberID" name="memberID">
          <button type="submit" class="btn btn-primary btn-block">
            <i class="fas fa-check-circle mr-2"></i> Submit Payment
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
  // SweetAlert integration after form submission
  document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    Swal.fire({
      icon: 'success',
      title: 'Payment Processed',
      text: 'Your payment has been successfully submitted.',
    });
  });
</script>
