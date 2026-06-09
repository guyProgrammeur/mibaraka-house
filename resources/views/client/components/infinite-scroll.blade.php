@push('scripts')
<script>
    let loading = false;
    let page = 1;
    let hasMorePages = {{ $products->hasMorePages() ? 'true' : 'false' }};
    
    function loadMore() {
        if (loading || !hasMorePages) return;
        
        loading = true;
        page++;
        
        fetch(window.location.href + '?page=' + page, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newProducts = doc.querySelector('.products-grid');
            const pagination = doc.querySelector('.pagination-container');
            
            if (newProducts) {
                document.querySelector('.products-grid').insertAdjacentHTML('beforeend', newProducts.innerHTML);
            }
            
            if (pagination && pagination.innerHTML.trim() === '') {
                hasMorePages = false;
            }
            
            loading = false;
        })
        .catch(() => {
            loading = false;
        });
    }
    
    // Infinite scroll
    window.addEventListener('scroll', () => {
        if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 500) {
            loadMore();
        }
    });
</script>
@endpush