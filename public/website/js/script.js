const body = document.body;

$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('input[name="_token"]').val()
    }
});
$(document).ready(function () {
    $('.select2').select2({
        width: '100%',
        allowClear: false,
        dropdownParent: $('#registerModal'),
        minimumResultsForSearch: 0
    });

    $(document).on('select2:open', (e) => {
        setTimeout(() => {
            const searchInput = document.querySelector('.select2-search__field');
            if (searchInput) searchInput.focus();

            const $select = $(e.target);
            const $parentGroup = $select.closest('.email-input-group');

            if ($parentGroup.length) {
                const fullWidth = $parentGroup.outerWidth();

                const innerSelectLeft = $select.next('.select2-container').offset().left;
                const outerGroupLeft = $parentGroup.offset().left;
                const offsetDiff = innerSelectLeft - outerGroupLeft;

                document.documentElement.style.setProperty('--dynamic-width', fullWidth + 'px');
                document.documentElement.style.setProperty('--dynamic-margin', `-${offsetDiff}px`);
            }
        }, 10);
    });
});
