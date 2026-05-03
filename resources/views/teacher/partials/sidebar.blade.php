<style>
:root{
    --tsb-w: 220px;
    --tsb-w-collapsed: 64px;
    --tsb-bg: #fff;
    --tsb-border: rgba(5,150,105,0.08);
    --tsb-green: #059669;
    --tsb-active-bg: rgba(5,150,105,0.06);
    --tsb-active-text: #059669;
    --tsb-hover-bg: rgba(5,150,105,0.03);
    --tsb-text: #5A6A7A;
    --tsb-label: rgba(5,150,105,0.5);
    --tsb-transition: 0.25s cubic-bezier(0.16,1,0.3,1);
}

#teacherSidebar{
    width: var(--tsb-w);
    flex-shrink: 0;
    background: var(--tsb-bg);
    border-right: 1px solid var(--tsb-border);
    position: sticky;
    top: 62px;
    height: calc(100vh - 62px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    transition: width var(--tsb-transition);
    z-index: 40;
    box-shadow: 2px 0 12px rgba(5,150,105,0.04);
    font-family: 'DM Sans', sans-serif;
}
#teacherSidebar.collapsed{ width: var(--tsb-w-collapsed); }

.tsb-scroll{
    flex: 1;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 16px 0 24px;
    scrollbar-width: thin;
    scrollbar-color: rgba(5,150,105,0.1) transparent;
}
.tsb-scroll::-webkit-scrollbar{ width: 3px; }
.tsb-scroll::-webkit-scrollbar-track{ background: transparent; }
.tsb-scroll::-webkit-scrollbar-thumb{ background: rgba(5,150,105,0.12); border-radius: 2px; }

.tsb-toggle{
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 0 16px;
    border-top: 1px solid var(--tsb-border);
    flex-shrink: 0;
}
.tsb-toggle-btn{
    width: 28px; height: 28px;
    border: 1px solid var(--tsb-border);
    border-radius: 6px;
    background: transparent;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--tsb-label);
    transition: all 0.2s;
}
.tsb-toggle-btn:hover{ background: var(--tsb-hover-bg); color: var(--tsb-green); border-color: rgba(5,150,105,0.2); }

.tsb-section{ margin-top: 4px; }
.tsb-section-header{
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 18px 6px;
    cursor: pointer;
    user-select: none;
}
.tsb-section-label{
    font-size: 8px;
    letter-spacing: 3px;
    text-transform: uppercase;
    color: var(--tsb-green);
    font-weight: 600;
    white-space: nowrap;
    overflow: hidden;
    transition: opacity var(--tsb-transition);
}
.tsb-section-chevron{
    color: var(--tsb-label);
    transition: transform 0.2s;
    flex-shrink: 0;
    opacity: 0.7;
}
.tsb-section.collapsed-section .tsb-section-chevron{ transform: rotate(-90deg); }
.tsb-section-body{
    overflow: hidden;
    transition: max-height 0.3s cubic-bezier(0.16,1,0.3,1);
    max-height: 600px;
}
.tsb-section.collapsed-section .tsb-section-body{ max-height: 0; }

.tsl-link{
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 18px;
    font-size: 11px;
    letter-spacing: 0.8px;
    text-transform: uppercase;
    color: var(--tsb-text);
    text-decoration: none;
    transition: all 0.18s;
    border-left: 2px solid transparent;
    white-space: nowrap;
    overflow: hidden;
    position: relative;
}
.tsl-link:hover{
    color: var(--tsb-active-text);
    background: var(--tsb-hover-bg);
    border-left-color: rgba(5,150,105,0.2);
    text-decoration: none;
}
.tsl-link.active{
    color: var(--tsb-active-text);
    background: var(--tsb-active-bg);
    border-left-color: var(--tsb-green);
    font-weight: 600;
}
.tsl-link svg{ flex-shrink: 0; opacity: 0.55; transition: opacity 0.18s; min-width: 14px; }
.tsl-link:hover svg,.tsl-link.active svg{ opacity: 1; }
.tsl-link-text{ transition: opacity var(--tsb-transition); overflow: hidden; }

.tsl-badge{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 18px; height: 18px;
    padding: 0 5px;
    border-radius: 9px;
    background: rgba(220,38,38,0.1);
    color: #DC2626;
    font-size: 9px;
    font-weight: 600;
    margin-left: auto;
    flex-shrink: 0;
}

.tsl-div{ height: 1px; background: var(--tsb-border); margin: 6px 16px; }
.tsl-view-tag{
    font-size: 8px; letter-spacing: 1px; text-transform: uppercase;
    color: #AAB8C8; margin-left: auto; flex-shrink: 0;
}

/* Collapsed */
#teacherSidebar.collapsed .tsl-link-text,
#teacherSidebar.collapsed .tsb-section-label,
#teacherSidebar.collapsed .tsb-section-chevron,
#teacherSidebar.collapsed .tsl-badge,
#teacherSidebar.collapsed .tsl-view-tag{
    opacity: 0; width: 0; pointer-events: none;
}
#teacherSidebar.collapsed .tsl-link{ padding: 10px 0; justify-content: center; border-left-color: transparent !important; }
#teacherSidebar.collapsed .tsl-link.active{ background: var(--tsb-active-bg); }
#teacherSidebar.collapsed .tsb-section-header{ justify-content: center; padding: 10px 0 4px; }
#teacherSidebar.collapsed .tsb-section-body{ max-height: 600px !important; }
#teacherSidebar.collapsed .tsb-toggle-btn svg{ transform: rotate(180deg); }

#teacherSidebar.collapsed .tsl-link::after{
    content: attr(data-label);
    position: absolute;
    left: calc(var(--tsb-w-collapsed) + 8px);
    top: 50%; transform: translateY(-50%);
    background: #1A2A4A; color: #fff;
    font-size: 10px; letter-spacing: 1px; text-transform: uppercase;
    padding: 5px 10px; border-radius: 4px;
    white-space: nowrap; pointer-events: none;
    opacity: 0; transition: opacity 0.15s; z-index: 999;
}
#teacherSidebar.collapsed .tsl-link:hover::after{ opacity: 1; }

/* Mobile overlay */
.tsb-overlay{
    display: none;
    position: fixed; inset: 0;
    background: rgba(10,20,40,0.4);
    backdrop-filter: blur(2px);
    z-index: 39;
}
.tsb-overlay.show{ display: block; }

@media(max-width:900px){
    #teacherSidebar{
        position: fixed; top: 0; left: 0; height: 100vh;
        z-index: 50;
        transform: translateX(-100%);
        transition: transform var(--tsb-transition);
        width: var(--tsb-w) !important;
        box-shadow: 4px 0 24px rgba(5,150,105,0.15);
    }
    #teacherSidebar.mobile-open{ transform: translateX(0); }
    .tsb-toggle{ display: none; }
}
</style>

<div class="tsb-overlay" id="tsbOverlay" onclick="closeTSidebar()"></div>

<aside id="teacherSidebar">
    <div class="tsb-scroll">

        {{-- Overview --}}
        <div class="tsb-section">
            <div class="tsb-section-header" onclick="toggleTSection(this)">
                <span class="tsb-section-label">Overview</span>
                <svg class="tsb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tsb-section-body">
                <a href="{{ route('teacher.dashboard') }}" class="tsl-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}" data-label="Dashboard">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    <span class="tsl-link-text">Dashboard</span>
                </a>
            </div>
        </div>

        <div class="tsl-div"></div>

        {{-- Academic --}}
        <div class="tsb-section">
            <div class="tsb-section-header" onclick="toggleTSection(this)">
                <span class="tsb-section-label">Academic</span>
                <svg class="tsb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tsb-section-body">
                <a href="{{ route('teacher.schedule') }}" class="tsl-link {{ request()->routeIs('teacher.schedule') ? 'active' : '' }}" data-label="Patch Schedule">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    <span class="tsl-link-text">Patch Schedule</span>
                    <span class="tsl-view-tag">View</span>
                </a>
                <a href="{{ route('teacher.courses') }}" class="tsl-link {{ request()->routeIs('teacher.courses*') ? 'active' : '' }}" data-label="My Courses">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    <span class="tsl-link-text">My Courses</span>
                </a>
            </div>
        </div>

        <div class="tsl-div"></div>

        {{-- Reports --}}
        <div class="tsb-section">
            <div class="tsb-section-header" onclick="toggleTSection(this)">
                <span class="tsb-section-label">Reports</span>
                <svg class="tsb-section-chevron" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
            <div class="tsb-section-body">
                <a href="{{ route('teacher.reports.index') }}" class="tsl-link {{ request()->routeIs('teacher.reports*') ? 'active' : '' }}" data-label="Reports">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                    <span class="tsl-link-text">Reports</span>
                </a>
            </div>
        </div>

    </div>

    <div class="tsb-toggle">
        <button class="tsb-toggle-btn" onclick="toggleTSidebar()" title="Toggle sidebar">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
    </div>
</aside>

<script>
const tSidebar   = document.getElementById('teacherSidebar');
const tsbOverlay = document.getElementById('tsbOverlay');
const T_PREF_KEY = 'teacher_sb_collapsed';

if(localStorage.getItem(T_PREF_KEY)==='1' && window.innerWidth>900){
    tSidebar.classList.add('collapsed');
}

function toggleTSidebar(){
    tSidebar.classList.toggle('collapsed');
    localStorage.setItem(T_PREF_KEY, tSidebar.classList.contains('collapsed')?'1':'0');
}

function openTSidebar(){
    tSidebar.classList.add('mobile-open');
    tsbOverlay.classList.add('show');
    document.body.style.overflow='hidden';
}
function closeTSidebar(){
    tSidebar.classList.remove('mobile-open');
    tsbOverlay.classList.remove('show');
    document.body.style.overflow='';
}

function toggleTSection(header){
    const section = header.closest('.tsb-section');
    const key = 'tsb_sec_' + header.querySelector('.tsb-section-label').textContent.trim();
    section.classList.toggle('collapsed-section');
    localStorage.setItem(key, section.classList.contains('collapsed-section')?'1':'0');
}

document.querySelectorAll('.tsb-section-header').forEach(header=>{
    const key='tsb_sec_'+header.querySelector('.tsb-section-label').textContent.trim();
    if(localStorage.getItem(key)==='1') header.closest('.tsb-section').classList.add('collapsed-section');
});

document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeTSidebar(); });
document.querySelectorAll('.tsl-link').forEach(link=>{
    link.addEventListener('click',()=>{ if(window.innerWidth<=900) closeTSidebar(); });
});
</script>