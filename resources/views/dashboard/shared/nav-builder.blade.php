        <div class="c-sidebar-brand">
            <img class="c-sidebar-brand-full" src="{{ asset('assets/brand/coreui-base-white.svg') }}" width="118" height="46" alt="CoreUI Logo">
            <img class="c-sidebar-brand-minimized" src="{{ asset('assets/brand/coreui-signet-white.svg') }}" width="118" height="46" alt="CoreUI Logo">
        </div>
        <ul class="c-sidebar-nav">
            <!--<li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="/countries">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-map"></use>
                    </svg>
                    Countries
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="/plans">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-list"></use>
                    </svg>
                    Plans
                </a>
            </li>-->
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="/products">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-qr-code"></use>
                    </svg>
                    Products
                </a>
            </li>
            <!--<li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="/alerts">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-qr-code"></use>
                    </svg>
                    Alerts
                </a>
            </li>
            <li class="c-sidebar-nav-item">
                <a class="c-sidebar-nav-link" href="/settings">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="/assets/icons/coreui/free-symbol-defs.svg#cui-qr-code"></use>
                    </svg>
                    Settings
                </a>
            </li>-->
            <li class="c-sidebar-nav-item">
                <form id="logout-form" action="/logout" method="POST" class="d-none">
                   {{ csrf_field() }}
                </form>
                <script>
                    function logout(){
                        document.getElementById('logout-form').submit();
                        return(false);
                    }
                </script>
                <a class="c-sidebar-nav-link" onclick="return logout();">
                    <svg class="c-sidebar-nav-icon">
                        <use xlink:href="assets/icons/coreui/free-symbol-defs.svg#cui-list"></use>
                    </svg>
                    Logout
                </a>
            </li>
        </ul>
        <button class="c-sidebar-minimizer c-class-toggler" type="button" data-target="_parent" data-class="c-sidebar-minimized"></button>
    </div>