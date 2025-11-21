<div class="modal fade" id="createShipmentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <form method="POST" action="{{ route('shipping.store') }}">
      @csrf
      <input type="hidden" name="order_id" id="modalOrderId">

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Purchase Label</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <ul class="nav nav-tabs mb-3">
            <li class="nav-item">
              <button class="nav-link active" id="tabFromOrder" type="button">
                From Order
              </button>
            </li>
            <li class="nav-item">
              <button class="nav-link" id="tabManual" type="button">
                Manual
              </button>
            </li>
          </ul>
          <div class="row g-3">
            <div class="col-md-6 from-order-only">
              <label class="form-label">Sender (From)</label>
              <input type="text" id="autoSender" class="form-control" readonly>
              <input type="hidden" name="sender_id" id="autoSenderId">
            </div>

            <div class="col-md-6 from-order-only">
              <label class="form-label">Receiver (To)</label>
              <input type="text" id="autoReceiver" class="form-control" readonly>
              <input type="hidden" name="receiver_id" id="autoReceiverId">
            </div>

            <!-- MANUAL → dropdowns -->
            <div class="col-md-6 manual-only d-none">
              <label class="form-label">Sender</label>
              <select name="sender_id" id="manualSender" class="form-select">
                <option value="">-- Select Sender --</option>
                @foreach($contacts as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6 manual-only d-none">
              <label class="form-label">Receiver</label>
              <select name="receiver_id" id="manualReceiver" class="form-select">
                <option value="">-- Select Receiver --</option>
                @foreach($contacts as $c)
                <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
              </select>
            </div>

            <!-- BOX (shared) -->
            <div class="col-md-6">
              <label class="form-label">Box</label>
              <select name="box_id" id="modalBox" class="form-select">
                <option value="">-- Select Box --</option>
                @foreach($boxes as $b)
                  @php
                    $totalOz = $b->empty_weight * 35.2739619;
                    $lb     = floor($totalOz / 16);
                    $oz     = $totalOz % 16;
                  @endphp
                  <option value="{{ $b->id }}"
                          data-length="{{ $b->length }}"
                          data-width="{{ $b->width }}"
                          data-height="{{ $b->height }}"
                          data-weight-lb="{{ $lb }}"
                          data-weight-oz="{{ $oz }}">
                      {{ $b->name }}
                  </option>
                @endforeach
              </select>
            </div>

            <!-- Box details -->
            <div class="col-md-6">
              <label class="form-label">Box Dimensions</label>
              <input type="text" id="modalBoxDimensions" class="form-control" readonly>
            </div>

            <!-- Product Weight -->
            <div class="col-md-6">
              <label class="form-label">Product Weight</label>
              <div class="input-group">
                <input type="number" id="modalProductWeightLb" name="product_weight_lb"
                       class="form-control" placeholder="LB" min="0">
                <span class="input-group-text">lb</span>
                <input type="number" step="0.1" id="modalProductWeightOz"
                       name="product_weight_oz" class="form-control"
                       placeholder="OZ" min="0">
                <span class="input-group-text">oz</span>
              </div>
            </div>

            <!-- Total Weight -->
            <div class="col-md-6">
              <label class="form-label">Total Weight</label>
              <input type="text" id="modalTotalWeight" class="form-control" readonly>
            </div>

            <!-- Carrier -->
            <div class="col-md-6">
              <label class="form-label">Carrier / Service</label>
              <select name="service_code" id="carrierServices" class="form-select">
                <option value="ups">UPS</option>
                <option value="usps">USPS</option>
              </select>
            </div>

            <!-- Rate -->
            <div class="col-md-6">
              <label class="form-label">Rate</label>
              <input type="text" id="selectedRate" class="form-control" readonly>
            </div>

          </div>
        </div>

        <div class="modal-footer">
          <button type="submit" class="btn btn-success">Buy & Print</button>
        </div>

      </div>
    </form>
  </div>
</div>

<script>
    const tabFromOrder = document.getElementById("tabFromOrder");
    const tabManual = document.getElementById("tabManual");

    tabFromOrder.addEventListener("click", () => switchMode("order"));
    tabManual.addEventListener("click", () => switchMode("manual"));

    function switchMode(mode) {
        const fromOrderElems = document.querySelectorAll(".from-order-only");
        const manualElems = document.querySelectorAll(".manual-only");
        

        const modalDom = document.getElementById("createShipmentModal");
        const modalForm = modalDom.querySelector("form");
        modalForm.reset();

        if (mode === "order") {
            fromOrderElems.forEach(e => e.classList.remove("d-none"));
            manualElems.forEach(e => e.classList.add("d-none"));
            tabFromOrder.classList.add("active");
            tabManual.classList.remove("active");

            const orderID = window.orderId;
            const order = window.openOrders.find(o => o.id == orderID);

            if (order) {
                document.getElementById("autoSender").value = order.sender_name || '';
                document.getElementById("autoSenderId").value = order.sender_id || '';
                document.getElementById("autoReceiver").value = order.receiver_name || '';
                document.getElementById("autoReceiverId").value = order.receiver_id || '';
                document.getElementById("modalBox").value = order.box_id || '';
                document.getElementById("modalProductWeightLb").value = order.product_weight_lb || '';
                document.getElementById("modalProductWeightOz").value = order.product_weight_oz || '';
            }

        } else {
            fromOrderElems.forEach(e => e.classList.add("d-none"));
            manualElems.forEach(e => e.classList.remove("d-none"));
            tabFromOrder.classList.remove("active");
            tabManual.classList.add("active");
        }
    }

    document.querySelector('#createShipmentModal form')
        .addEventListener('submit', function (e) {

            console.log("Form is being submitted…");

        });
</script>
