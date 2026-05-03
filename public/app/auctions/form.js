/* =============================================
   Auctions Module - Form Page Scripts
   public/app/auctions/form.js
   ============================================= */

$(function () {

    // ── Choices.js multiselect for participants ──
    var participantEl = document.getElementById('participants');
    if (participantEl) {
        new Choices(participantEl, {
            removeItemButton: true,
            searchEnabled: true,
            searchPlaceholderValue: 'Search participants...',
            placeholderValue: 'Select participants (RA role)',
            noResultsText: 'No participants found',
            noChoicesText: 'No participants available',
            itemSelectText: '',
            classNames: { containerOuter: 'choices' }
        });
    }

    // ── Choices.js multiselect for NPV categories ──
    var npvCatEl = document.getElementById('npv_categories');
    if (npvCatEl) {
        new Choices(npvCatEl, {
            removeItemButton: true,
            searchEnabled: true,
            searchPlaceholderValue: 'Search categories...',
            placeholderValue: 'Select NPV categories',
            noResultsText: 'No categories found',
            noChoicesText: 'No categories available',
            itemSelectText: '',
            classNames: { containerOuter: 'choices' }
        });
    }

    // ── NPVP Add More ──
    var npvpIndex = $('#npvp-wrapper .npvp-row').length;

    function buildNpvpRow(index) {
        return `
        <div class="npvp-row" id="npvp-row-${index}">
            <button type="button" class="btn btn-danger btn-sm btn-remove-npvp" onclick="removeNpvp(${index})">
                <i class="fas fa-times"></i>
            </button>
            <div class="row">
                <div class="col-md-6 col-12">
                    <div class="form-group mb-2">
                        <label class="mb-1">Period (days)</label>
                        <input type="number" min="1" name="npvp[${index}][period]"
                            class="form-control form-control-sm npvp-period"
                            placeholder="e.g. 30">
                        <div class="npvp-error text-danger" style="font-size:12px; display:none;"></div>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <div class="form-group mb-2">
                        <label class="mb-1">Percentage Value <span class="text-danger">*</span></label>
                        <input type="number" step="0.0000001" name="npvp[${index}][percentage_value]"
                            class="form-control form-control-sm npvp-pct"
                            placeholder="e.g. 8.5000000">
                        <div class="npvp-pct-error text-danger" style="font-size:12px; display:none;"></div>
                    </div>
                </div>
            </div>
        </div>`;
    }

    $('#btn-add-npvp').on('click', function () {
        $('#npvp-wrapper').append(buildNpvpRow(npvpIndex));
        npvpIndex++;
        updateNpvpEmpty();
    });

    window.removeNpvp = function (index) {
        $('#npvp-row-' + index).remove();
        updateNpvpEmpty();
    };

    function updateNpvpEmpty() {
        $('#npvp-wrapper .npvp-row').length === 0
            ? $('#npvp-empty').show()
            : $('#npvp-empty').hide();
    }

    // ── NPVP period validation ──
    function validateNpvp() {
        var rows = $('#npvp-wrapper .npvp-row');
        var valid = true;
        var prevPeriod = 0;

        rows.find('.npvp-period').removeClass('is-invalid');
        rows.find('.npvp-error').hide().text('');
        rows.find('.npvp-pct').removeClass('is-invalid');
        rows.find('.npvp-pct-error').hide().text('');

        rows.each(function (i) {
            var $periodInput = $(this).find('.npvp-period');
            var $periodErr   = $(this).find('.npvp-error');
            var period       = parseInt($periodInput.val());

            if (isNaN(period) || period <= 0) {
                $periodInput.addClass('is-invalid');
                $periodErr.text('Period must be greater than 0.').show();
                valid = false;
            } else if (i > 0 && period <= prevPeriod) {
                $periodInput.addClass('is-invalid');
                $periodErr.text('Period must be greater than previous row\'s period (' + prevPeriod + ').').show();
                valid = false;
            }

            var $pctInput = $(this).find('.npvp-pct');
            var $pctErr   = $(this).find('.npvp-pct-error');
            var pct       = parseFloat($pctInput.val());

            if ($pctInput.val() === '' || isNaN(pct) || pct < 0) {
                $pctInput.addClass('is-invalid');
                $pctErr.text('Percentage value is required and must be 0 or greater.').show();
                valid = false;
            }

            prevPeriod = isNaN(period) ? prevPeriod : period;
        });

        return valid;
    }

    $(document).on('change', '.npvp-period', function () {
        validateNpvp();
    });

    $(document).on('change', '.npvp-pct', function () {
        validateNpvp();
    });

    function validateEndingPeriod() {
        var $ep    = $('[name="ending_period"]');
        var $epErr = $('#ending-period-error');
        var ep     = parseInt($ep.val());

        var maxPeriod = 0;
        $('#npvp-wrapper .npvp-period').each(function () {
            var v = parseInt($(this).val()) || 0;
            if (v > maxPeriod) maxPeriod = v;
        });

        $ep.removeClass('is-invalid');
        $epErr.hide().text('');

        if (isNaN(ep) || ep < 1) {
            $ep.addClass('is-invalid');
            $epErr.text('Ending period is required and must be at least 1.').show();
            return false;
        }

        if (maxPeriod > 0 && ep <= maxPeriod) {
            $ep.addClass('is-invalid');
            $epErr.text('Ending period must be greater than the maximum NPVP period (' + maxPeriod + ').').show();
            return false;
        }

        return true;
    }

    $(document).on('change', '[name="ending_period"]', function () {
        validateEndingPeriod();
    });

    function validateInitialNpvValue() {
        var $input = $('[name="initial_npv_value"]');
        var val    = parseFloat($input.val());

        $input.removeClass('is-invalid');
        $input.next('.npv-value-error').hide().text('');

        if (isNaN(val) || val <= 0) {
            $input.addClass('is-invalid');
            $input.next('.npv-value-error').text('Initial NPV value is required and must be greater than 0.').show();
            return false;
        }
        return true;
    }

    $(document).on('change', '[name="initial_npv_value"]', function () {
        validateInitialNpvValue();
    });

    function validateIncrementAmountType() {
        var $select = $('[name="increment_amount_type"]');
        var $err    = $select.closest('.form-group').find('.invalid-feedback');

        $select.removeClass('is-invalid');
        $err.hide().text('');

        if (!$select.val()) {
            $select.addClass('is-invalid');
            $err.text('Increment Amount Type is required.').show();
            return false;
        }
        return true;
    }

    $(document).on('change', '[name="increment_amount_type"]', function () {
        validateIncrementAmountType();
    });

    // validate on form submit
    $('form').on('submit', function (e) {
        var valid = validateNpvp() & validateEndingPeriod() & validateInitialNpvValue() & validateIncrementAmountType();

        if (!valid) {
            e.preventDefault();
            $('html, body').animate({
                scrollTop: $('.is-invalid').first().offset().top - 100
            }, 400);
        }
    });

    updateNpvpEmpty();

});
