<!-- Vendor Scripts Start -->
<script
        src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>
<script src="/js/vendor/OverlayScrollbars.min.js"></script>
<script src="/js/vendor/autoComplete.min.js"></script>
<script src="/js/vendor/clamp.min.js"></script>
<script src="/icon/acorn-icons.js"></script>
<script src="/icon/acorn-icons-interface.js"></script>
<script src="/js/pages/logout.js"></script>

<!-- Vendor Scripts End -->


@yield('js_vendor')
<!-- Template Base Scripts Start -->
<script src="/js/base/helpers.js"></script>
<script src="/js/base/globals.js"></script>
<script src="/js/base/nav.js"></script>
<script src="/js/base/search.js"></script>
<script src="/js/base/settings.js"></script>
<!-- Template Base Scripts End -->
<!-- Page Specific Scripts Start -->
@yield('js_page')
<script src="/js/common.js"></script>
<script src="/js/scripts.js"></script>
<!-- Page Specific Scripts End -->
