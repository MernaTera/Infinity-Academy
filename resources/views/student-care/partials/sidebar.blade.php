<style>
:root{
    --scsb-w: 230px;
    --scsb-w-col: 64px;
    --scsb-bg: #fff;
    --scsb-border: rgba(27,79,168,0.08);
    --scsb-blue: #1B4FA8;
    --scsb-orange: #F5911E;
    --scsb-active-bg: rgba(27,79,168,0.06);
    --scsb-active: #1B4FA8;
    --scsb-hover-bg: rgba(27,79,168,0.03);
    --scsb-text: #5A6A7A;
    --scsb-transition: 0.25s cubic-bezier(0.16,1,0.3,1);
}

#scSidebar{
    width: var(--scsb-w);
    flex-shrink: 0;
    background: var(--scsb-bg);
    border-right: 1px solid var(--scsb-border);
    position: sticky;
    top: 62px;
    height: calc(100vh - 62px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: width var(--scsb-transition);
    z-index: 40;
    box-shadow: 2px 0 12px rgba(27,79,168,0.04);
    font-family: 'DM Sans', sans-serif;
}
#scSidebar.collapsed{ width: var(--scsb-w-col); }

.scsb-scroll{
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 0 24px;
    scrollbar-width: thin;
    scrollbar-color: rgba(27,79,168,0.1) transparent;
}
.scsb-scroll::-webkit-scrollbar{ width: 3px; }
.scsb-scroll::-webkit-scrollbar-thumb{ background: rgba(27,79,168,0.12); border-radius: 2px; }

.scsb-toggle{
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 16px;
    border-top: 1px solid var(--scsb-border);
    flex-shrink: 0;
}
.scsb-toggle-btn{
    width: 28px; height: 28px;
    border: 1px solid var(--scsb-border);
    border-radius: 6px;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #AAB8C8;
    transition: all 0.2s;
}
.scsb-toggle-btn:hover{ background: var(--scsb-hover-bg); color: var(--scsb-blue); border-color: rgba(27,79,168,0.2); }

.scsb-section{ margin-top: 4px; }
.scsb-section-header{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px 6px;
    cursor: pointer;
    user-select: none;
}
.scsb-section-label{
    font-size: 8px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--scsb-orange);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    transition: opacity var(--scsb-transition);
}
.scsb-section-chevron{
    color: #AAB8C8;
    transition: transform 0.2s;
    flex-shrink: 0;
    opacity: 0.6;
}
.scsb-section.collapsed-section .scsb-section-chevron{ transform: rotate(-90deg); }
.scsb-section-body{
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.16,1,0.3,1);
    max-height: 600px;
}
.scsb-section.collapsed-section .scsb-section-body{ max-height: 0; }

.scsl-link{
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 18px;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--scsb-text);
    text-decoration: none;
    transition: all 0.18s;
    border-left: 2px solid transparent;
    white-space: nowrap;
    overflow: hidden;
    position: relative;
}
.scsl-link:hover{
    color: var(--scsb-active);
    background: var(--scsb-hover-bg);
    border-left-color: rgba(27,79,168,0.2);
    text-decoration: none;
}
.scsl-link.active{
    color: var(--scsb-active);
    background: var(--scsb-active-bg);
    border-left-color: var(--scsb-blue);
    font-weight: 600;
}
.scsl-link svg{ flex-shrink: 0; opacity: 0.55; transition: opacity 0.18s; min-width: 14px; }
.scsl-link:hover svg,.scsl-link.active svg{ opacity: 1; }
.scsl-link-text{ transition: opacity var(--scsb-transition); overflow: hidden; }

.scsl-div{ height: 1px; background: var(--scsb-border); margin: 6px 16px; }

/* Collapsed */
#scSidebar.collapsed .scsl-link-text,
#scSidebar.collapsed .scsb-section-label,
#scSidebar.collapsed .scsb-section-chevron{ opacity: 0; width: 0; pointer-events: none; }
#scSidebar.collapsed .scsl-link{ padding: 10px 0; justify-content: center; border-left-color: transparent !important; }
#scSidebar.collapsed .scsl-link.active{ background: var(--scsb-active-bg); }
#scSidebar.collapsed .scsb-section-header{ justify-content: center; padding: 10px 0 4px; }
#scSidebar.collapsed .scsb-section-body{ max-height: 600px !important; }
#scSidebar.collapsed .scsb-toggle-btn svg{ transform: rotate(180deg); }

#scSidebar.collapsed .scsl-link::after{
    content: attr(data-label);
    position: absolute;
    left: calc(var(--scsb-w-col) + 8px);
    top: 50%; transform: translateY(-50%);
    background: #1A2A4A; color: #fff;
    font-size: 10px; letter-spacing: 1px; text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    white-space: nowrap; pointer-events: none;
    opacity: 0; transition: opacity 0.15s; z-index: 999;
}
#scSidebar.collapsed .scsl-link:hover::after{ opacity: 1; }

/* Mobile overlay */
.scsb-overlay{
    display: none;
    position: fixed; inset: 0;
    background: rgba(10,20,40,0.4);
    backdrop-filter: blur(2px);
    z-index: 39;
}
.scsb-overlay.show{ display: block; }

@media(max-width:900px){
    #scSidebar{
        position: fixed; top: 0; left: 0; height: 100vh;
        z-index: 50;
        transform: translateX(-100%);
        transition: transform var(--scsb-transition);
        width: var(--scsb-w) !important;
        box-shadow: 4px 0 24px rgba(27,79,168,0.15);
    }
    #scSidebar.mobile-open{ transform: translateX(0); }
    .scsb-toggle{ display: none; }
}
</style>

<div class="scsb-overlay" id="scsbOverlay" onclick="scCloseSidebar()"></div>

<aside id="scSidebar">
    <div class="scsb-scroll">

        {{-- Overview --}}
        <div class="scsb-section">
            <div class="scsb-section-header" onclick="scToggleSection(this)">
                <span class="scsb-section-label">Student Care</span>
                <svg class="scsb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="scsb-section-body">
                <a href="{{ route('student-care.dashboard') }}" class="scsl-link {{ request()->routeIs('student-care.dashboard') ? 'active' : '' }}" data-label="Dashboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span class="scsl-link-text">Dashboard</span>
                </a>
            </div>
        </div>

        <div class="scsl-div"></div>

        {{-- Enrollment --}}
        <div class="scsb-section">
            <div class="scsb-section-header" onclick="scToggleSection(this)">
                <span class="scsb-section-label">Enrollment</span>
                <svg class="scsb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="scsb-section-body">
                <a href="{{ route('student-care.waiting-list') }}" class="scsl-link {{ request()->routeIs('student-care.waiting-list') ? 'active' : '' }}" data-label="Waiting List">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <span class="scsl-link-text">Waiting List</span>
                </a>
            </div>
        </div>

        <div class="scsl-div"></div>

        {{-- Courses --}}
        <div class="scsb-section">
            <div class="scsb-section-header" onclick="scToggleSection(this)">
                <span class="scsb-section-label">Courses</span>
                <svg class="scsb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="scsb-section-body">
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
            </div>
        </div>

    </div>

    <div class="scsb-toggle">
        <button class="scsb-toggle-btn" onclick="scToggleSidebar()" title="Toggle sidebar">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
    </div>
</aside>

<script>
const scSidebar   = document.getElementById('scSidebar');
const scsbOverlay = document.getElementById('scsbOverlay');
const SC_PREF_KEY = 'sc_sb_collapsed';

if(localStorage.getItem(SC_PREF_KEY)==='1' && window.innerWidth>900){
    scSidebar.classList.add('collapsed');
}

function scToggleSidebar(){
    scSidebar.classList.toggle('collapsed');
    localStorage.setItem(SC_PREF_KEY, scSidebar.classList.contains('collapsed')?'1':'0');
}

function scOpenSidebar(){
    scSidebar.classList.add('mobile-open');
    scsbOverlay.classList.add('show');
    document.body.style.overflow='hidden';
}
function scCloseSidebar(){
    scSidebar.classList.remove('mobile-open');
    scsbOverlay.classList.remove('show');
    document.body.style.overflow='';
}

function scToggleSection(header){
    const section=header.closest('.scsb-section');
    const key='scsb_sec_'+header.querySelector('.scsb-section-label').textContent.trim();
    section.classList.toggle('collapsed-section');
    localStorage.setItem(key, section.classList.contains('collapsed-section')?'1':'0');
}

document.querySelectorAll('.scsb-section-header').forEach(header=>{
    const key='scsb_sec_'+header.querySelector('.scsb-section-label').textContent.trim();
    if(localStorage.getItem(key)==='1') header.closest('.scsb-section').classList.add('collapsed-section');
});

document.addEventListener('keydown',e=>{ if(e.key==='Escape') scCloseSidebar(); });
document.querySelectorAll('.scsl-link').forEach(link=>{
    link.addEventListener('click',()=>{ if(window.innerWidth<=900) scCloseSidebar(); });
});
</script>