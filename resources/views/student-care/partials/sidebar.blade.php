<style>
:root{
    --scsc-w: 230px;
    --scsc-w-col: 64px;
    --scsc-bg: #fff;
    --scsc-border: rgba(27,79,168,0.08);
    --scsc-blue: #1B4FA8;
    --scsc-orange: #F5911E;
    --scsc-active-bg: rgba(27,79,168,0.06);
    --scsc-active: #1B4FA8;
    --scsc-hover-bg: rgba(27,79,168,0.03);
    --scsc-text: #5A6A7A;
    --scsc-transition: 0.25s cubic-bezier(0.16,1,0.3,1);
}

#scSidebar{
    width: var(--scsc-w);
    flex-shrink: 0;
    background: var(--scsc-bg);
    border-right: 1px solid var(--scsc-border);
    position: sticky;
    top: 62px;
    height: calc(100vh - 62px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: width var(--scsc-transition);
    z-index: 40;
    box-shadow: 2px 0 12px rgba(27,79,168,0.04);
    font-family: 'DM Sans', sans-serif;
}
/* ── COLLAPSED ── */
#scSidebar.collapsed { width: var(--scsc-w-col); }
#scSidebar.collapsed .sl-txt,
#scSidebar.collapsed .sl-bdg { opacity:0; width:0; pointer-events:none; }
#scSidebar.collapsed .sl {
    padding: 8px 0;
    justify-content: center;
    border-left-color: transparent !important;
}
#scSidebar.collapsed .sl.active { background: var(--scsc-active-bg); }
#scSidebar.collapsed .scsc-toggle-btn svg { transform: rotate(180deg); }

/* Tooltip */
#scSidebar.collapsed .sl::after {
    content: attr(data-tip);
    position: absolute;
    left: calc(var(--scsc-w-col));
    top: 50%; transform: translateY(-50%);
    background: #1A2A4A; color: #fff;
    font-size: 12px; letter-spacing: 1px;
    text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    white-space: nowrap; pointer-events: none;
    opacity: 0; transition: opacity 0.15s; z-index: 999;
}
#scSidebar.collapsed .sl:hover::after { opacity: 1; }
.scsc-scroll{
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 0 24px;
    scrollbar-width: thin;
    scrollbar-color: rgba(27,79,168,0.1) transparent;
}
.scsc-scroll::-webkit-scrollbar{ width: 3px; }
.scsc-scroll::-webkit-scrollbar-thumb{ background: rgba(27,79,168,0.12); border-radius: 2px; }

.scsc-toggle{
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 16px;
    border-top: 1px solid var(--scsc-border);
    flex-shrink: 0;
}
.scsc-toggle-btn{
    width: 28px; height: 28px;
    border: 1px solid var(--scsc-border);
    border-radius: 6px;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #AAB8C8;
    transition: all 0.2s;
}
.scsc-toggle-btn:hover{ background: var(--scsc-hover-bg); color: var(--scsc-blue); border-color: rgba(27,79,168,0.2); }

.scsc-section{ margin-top: 4px; }
.scsc-section-header{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px 6px;
    cursor: pointer;
    user-select: none;
}
.scsc-section-label{
    font-size: 8px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--scsc-orange);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    transition: opacity var(--scsc-transition);
}
.scsc-section-chevron{
    color: #AAB8C8;
    transition: transform 0.2s;
    flex-shrink: 0;
    opacity: 0.6;
}
.scsc-section.collapsed-section .scsc-section-chevron{ transform: rotate(-90deg); }
.scsc-section-body{
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.16,1,0.3,1);
    max-height: 600px;
}
.scsc-section.collapsed-section .scsc-section-body{ max-height: 0; }

.scsl-link{
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 18px;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--scsc-text);
    text-decoration: none;
    transition: all 0.18s;
    border-left: 2px solid transparent;
    white-space: nowrap;
    overflow: hidden;
    position: relative;
}
.scsl-link:hover{
    color: var(--scsc-active);
    background: var(--scsc-hover-bg);
    border-left-color: rgba(27,79,168,0.2);
    text-decoration: none;
}
.scsl-link.active{
    color: var(--scsc-active);
    background: var(--scsc-active-bg);
    border-left-color: var(--scsc-blue);
    font-weight: 600;
}
.scsl-link svg{ flex-shrink: 0; opacity: 0.55; transition: opacity 0.18s; min-width: 14px; }
.scsl-link:hover svg,.scsl-link.active svg{ opacity: 1; }
.scsl-link-text{ transition: opacity var(--scsc-transition); overflow: hidden; }

.scsl-div{ height: 1px; background: var(--scsc-border); margin: 6px 16px; }

/* Collapsed */
#scSidebar.collapsed .scsl-link-text,
#scSidebar.collapsed .scsc-section-label,
#scSidebar.collapsed .scsc-section-chevron{ opacity: 0; width: 0; pointer-events: none; }
#scSidebar.collapsed .scsl-link{ padding: 10px 0; justify-content: center; border-left-color: transparent !important; }
#scSidebar.collapsed .scsl-link.active{ background: var(--scsc-active-bg); }
#scSidebar.collapsed .scsc-section-header{ justify-content: center; padding: 10px 0 4px; }
#scSidebar.collapsed .scsc-section-body{ max-height: 600px !important; }
#scSidebar.collapsed .scsc-toggle-btn svg{ transform: rotate(180deg); }

#scSidebar.collapsed .scsl-link::after{
    content: attr(data-label);
    position: absolute;
    left: calc(var(--scsc-w-col) + 8px);
    top: 50%; transform: translateY(-50%);
    background: #1A2A4A; color: #fff;
    font-size: 10px; letter-spacing: 1px; text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    white-space: nowrap; pointer-events: none;
    opacity: 0; transition: opacity 0.15s; z-index: 999;
}
#scSidebar.collapsed .scsl-link:hover::after{ opacity: 1; }

/* Mobile overlay */
.scsc-overlay{
    display: none;
    position: fixed; inset: 0;
    background: rgba(10,20,40,0.4);
    backdrop-filter: blur(2px);
    z-index: 39;
}
.scsc-overlay.show{ display: block; }

@media(max-width:900px){
    #scSidebar{
        position: fixed; top: 0; left: 0; height: 100vh;
        z-index: 50;
        transform: translateX(-100%);
        transition: transform var(--scsc-transition);
        width: var(--scsc-w) !important;
        box-shadow: 4px 0 24px rgba(27,79,168,0.15);
    }
    #scSidebar.mobile-open{ transform: translateX(0); }
    .scsc-toggle{ display: none; }
}
@media(max-width:768px){
    #scSidebar {
        position:fixed; top:0; left:0; height:100vh;
        z-index:50; transform:translateX(-100%);
        transition:transform var(--scsc-ease), width var(--scsc-ease);
        width:var(--scsc-w) !important;
        box-shadow:4px 0 24px rgba(27,79,168,0.15);
    }
    #scSidebar.mobile-open { transform:translateX(0); }
    .scsc-foot { display:none; }
}
</style>

<div class="scsc-overlay" id="scscOverlay" onclick="scCloseSidebar()"></div>

<aside id="scSidebar">
    <div class="scsc-scroll">

        {{-- Overview --}}
        <div class="scsc-section">
            <div class="scsc-section-header" onclick="scToggleSection(this)">
                <span class="scsc-section-label">Student Care</span>
                <svg class="scsc-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="scsc-section-body">
                <a href="{{ route('student-care.dashboard') }}" class="scsl-link {{ request()->routeIs('student-care.dashboard') ? 'active' : '' }}" data-label="Dashboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span class="scsl-link-text">Dashboard</span>
                </a>
            </div>
        </div>

        <div class="scsl-div"></div>

        {{-- Enrollment --}}
        <div class="scsc-section">
            <div class="scsc-section-header" onclick="scToggleSection(this)">
                <span class="scsc-section-label">Enrollment</span>
                <svg class="scsc-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="scsc-section-body">
                <a href="{{ route('student-care.waiting-list') }}" class="scsl-link {{ request()->routeIs('student-care.waiting-list') ? 'active' : '' }}" data-label="Waiting List">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <span class="scsl-link-text">Waiting List</span>
                </a>
            </div>
        </div>

        <div class="scsl-div"></div>

        {{-- Courses --}}
        <div class="scsc-section">
            <div class="scsc-section-header" onclick="scToggleSection(this)">
                <span class="scsc-section-label">Courses</span>
                <svg class="scsc-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="scsc-section-body">
                <a href="{{ route('student-care.instances') }}" class="scsl-link {{ request()->routeIs('student-care.instances') ? 'active' : '' }}" data-label="Active Courses">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    <span class="scsl-link-text">Active Courses</span>
                </a>
                <a href="{{ route('student-care.instances.create') }}" class="scsl-link {{ request()->routeIs('student-care.instances.create') ? 'active' : '' }}" data-label="Create Course">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/></svg>
                    <span class="scsl-link-text">Create New Course</span>
                </a>
                <a href="{{ route('student-care.outstanding') }}" class="scsl-link {{ request()->routeIs('student-care.outstanding') ? 'active' : '' }}" data-label="Outstanding">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span class="scsl-link-text">Outstanding</span>
                </a>
                <a href="{{ route('student-care.postponed') }}" class="scsl-link {{ request()->routeIs('student-care.postponed') ? 'active' : '' }}" data-label="Postponed">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span class="scsl-link-text">Postponed</span>
                </a>
                <a href="{{ route('private-hours.index') }}" class="scsl-link {{ request()->routeIs('private-hours.*') ? 'active' : '' }}" data-label="Private Hours">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span class="scsl-link-text">Private Hours</span>
                </a>
                <a href="{{ route('packages-tracking.index') }}" class="scsl-link {{ request()->routeIs('packages-tracking.*') ? 'active' : '' }}" data-label="Level Packages">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
                    <span class="sl-link-text">Level Packages</span>
                </a>
            </div>
        </div>

    </div>

    <div class="scsc-toggle">
        <button class="scsc-toggle-btn" onclick="toggleSidebar()" title="Toggle sidebar">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
    </div>
</aside>

<script>
const scSidebar   = document.getElementById('scSidebar');
const scscOverlay = document.getElementById('scscOverlay');
const SC_PREF_KEY = 'sc_sc_collapsed';
const _sc = document.getElementById('scSidebar');
const _scToggleBtn = document.querySelector('.scsc-toggle-btn');

if(localStorage.getItem(SC_PREF_KEY)==='1' && window.innerWidth>900){
    scSidebar.classList.add('collapsed');
}

function toggleSidebar(){
    scSidebar.classList.toggle('collapsed');
    localStorage.setItem(SC_PREF_KEY, scSidebar.classList.contains('collapsed')?'1':'0');
}

function scOpenSidebar(){
    scSidebar.classList.add('mobile-open');
    scscOverlay.classList.add('show');
    document.body.style.overflow='hidden';
}
function scCloseSidebar(){
    scSidebar.classList.remove('mobile-open');
    scscOverlay.classList.remove('show');
    document.body.style.overflow='';
}

function scToggleSection(header){
    const section=header.closest('.scsc-section');
    const key='scsc_sec_'+header.querySelector('.scsc-section-label').textContent.trim();
    section.classList.toggle('collapsed-section');
    localStorage.setItem(key, section.classList.contains('collapsed-section')?'1':'0');
}

document.querySelectorAll('.scsc-section-header').forEach(header=>{
    const key='scsc_sec_'+header.querySelector('.scsc-section-label').textContent.trim();
    if(localStorage.getItem(key)==='1') header.closest('.scsc-section').classList.add('collapsed-section');
});

document.addEventListener('keydown',e=>{ if(e.key==='Escape') scCloseSidebar(); });
document.querySelectorAll('.scsl-link').forEach(link=>{
    link.addEventListener('click',()=>{ if(window.innerWidth<=900) scCloseSidebar(); });
});
</script>