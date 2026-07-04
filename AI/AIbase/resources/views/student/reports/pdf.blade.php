<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>RiseStar AI Report</title>
<style>
  body { font-family: 'DejaVu Sans', Arial, sans-serif; margin: 0; background: #fff; color: #1a1a2e; font-size: 12px; }
  /* Rainbow stripe */
  .stripe { height: 5px; background: linear-gradient(90deg, #4f46e5, #7c3aed, #db2777, #f59e0b); }

  /* Header */
  .header { background: #1a1a2e; padding: 24px 32px; display: flex; justify-content: space-between; align-items: center; }
  .brand { font-size: 20px; font-weight: 900; color: #fff; letter-spacing: 0.5px; }
  .brand span { color: #fbbf24; }
  .header-right { text-align: right; }
  .report-type { font-size: 9px; color: rgba(255,255,255,0.4); text-transform: uppercase; letter-spacing: 2px; }
  .report-date  { font-size: 11px; color: #fbbf24; font-weight: 700; margin-top: 3px; }

  /* Candidate bar */
  .candidate { background: #f3f4f6; border-bottom: 2px solid #e5e7eb; padding: 14px 32px; display: flex; justify-content: space-between; align-items: center; }
  .cand-name  { font-size: 15px; font-weight: 900; color: #111827; }
  .cand-meta  { font-size: 10px; color: #6b7280; margin-top: 2px; }
  .score-pill { background: #1a1a2e; color: #fbbf24; font-size: 22px; font-weight: 900; padding: 8px 20px; border-radius: 12px; }
  .score-pill small { font-size: 11px; color: rgba(255,255,255,0.5); display: block; text-align: center; font-weight: 600; }

  /* Body */
  .body { padding: 24px 32px; }
  .section-title { font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 2px; color: #4f46e5; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin: 20px 0 12px; }
  .section-title:first-child { margin-top: 0; }

  /* Category bars */
  .cat { margin-bottom: 10px; }
  .cat-row { display: flex; justify-content: space-between; margin-bottom: 4px; }
  .cat-name { font-size: 11px; font-weight: 700; color: #374151; }
  .cat-pct  { font-size: 11px; font-weight: 900; }
  .hi { color: #059669; } .md { color: #4f46e5; } .lo { color: #d97706; }
  .bar { height: 7px; border-radius: 99px; background: #f3f4f6; overflow: hidden; }
  .fill { height: 7px; border-radius: 99px; }
  .fill.hi { background: linear-gradient(90deg,#10b981,#34d399); }
  .fill.md { background: linear-gradient(90deg,#4f46e5,#818cf8); }
  .fill.lo { background: linear-gradient(90deg,#f59e0b,#fcd34d); }

  /* Insight box */
  .insight { background: #eef2ff; border-left: 4px solid #4f46e5; border-radius: 8px; padding: 14px 16px; font-size: 11px; color: #374151; line-height: 1.7; }

  /* Next steps */
  .steps { display: flex; gap: 10px; margin-top: 4px; }
  .step { flex: 1; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 10px 8px; text-align: center; }
  .step-e { font-size: 16px; }
  .step-t { font-size: 9px; font-weight: 800; color: #111827; margin-top: 4px; }

  /* Footer */
  .footer { margin-top: 24px; border-top: 1px solid #e5e7eb; padding: 10px 32px; display: flex; justify-content: space-between; font-size: 9px; color: #9ca3af; }
  .footer strong { color: #4f46e5; }
</style>
</head>
<body>

<div class="stripe"></div>

{{-- HEADER --}}
<div class="header">
  <div class="brand">Rise<span>Star</span> AI</div>
  <div class="header-right">
    <div class="report-type">Aptitude Assessment Report</div>
    <div class="report-date">{{ $date }}</div>
  </div>
</div>

{{-- CANDIDATE --}}
@php
  $pct = $attempt->percentage ?? 0;
  $grade = $pct >= 80 ? 'Excellent' : ($pct >= 60 ? 'Good' : ($pct >= 40 ? 'Average' : 'Improving'));
@endphp
<div class="candidate">
  <div>
    <div class="cand-name">{{ $user->name }}</div>
    <div class="cand-meta">{{ $user->school ?? '—' }} &nbsp;·&nbsp; Grade {{ $user->grade ?? 'N/A' }} &nbsp;·&nbsp; {{ $test->title ?? 'Aptitude Test' }}</div>
    <div class="cand-meta" style="margin-top:4px;">Score: <strong style="color:#111">{{ $attempt->score }} correct</strong> &nbsp;·&nbsp; Time: <strong style="color:#111">{{ floor($attempt->time_taken/60) }}m {{ $attempt->time_taken % 60 }}s</strong> &nbsp;·&nbsp; Grade: <strong style="color:#111">{{ $grade }}</strong></div>
  </div>
  <div class="score-pill">
    {{ $pct }}%
    <small>Overall</small>
  </div>
</div>

{{-- BODY --}}
<div class="body">

  <div class="section-title">Category Breakdown</div>
  @foreach($attempt->category_scores ?? [] as $category => $s)
    @php $p = $s['total'] > 0 ? round(($s['correct']/$s['total'])*100) : 0; $c = $p>=75?'hi':($p>=50?'md':'lo'); @endphp
    <div class="cat">
      <div class="cat-row">
        <span class="cat-name">{{ $category }}</span>
        <span class="cat-pct {{ $c }}">{{ $p }}% &nbsp;<span style="font-weight:400;color:#9ca3af;font-size:10px;">({{ $s['correct'] }}/{{ $s['total'] }})</span></span>
      </div>
      <div class="bar"><div class="fill {{ $c }}" style="width:{{ $p }}%"></div></div>
    </div>
  @endforeach

  <div class="section-title">AI Counselor Insight</div>
  <div class="insight">
    @if($pct >= 80) <strong>{{ $user->name }}</strong>, your outstanding <strong>{{ $pct }}%</strong> score reflects exceptional reasoning ability. Explore <strong>Engineering, Data Science, Medicine, or Research</strong> pathways. Generate your AI Career Blueprint on the platform for a personalized roadmap.
    @elseif($pct >= 60) <strong>{{ $user->name }}</strong>, your solid <strong>{{ $pct }}%</strong> shows strong analytical thinking. You're well-suited for <strong>Business, Computer Applications, or Healthcare</strong>. Run a Skill-Gap Analysis to find exactly what to improve next.
    @elseif($pct >= 40) <strong>{{ $user->name }}</strong>, your score of <strong>{{ $pct }}%</strong> shows developing potential. Focus practice on your weaker categories and use the AI Blueprint to discover career paths that match your current strengths.
    @else <strong>{{ $user->name }}</strong>, your <strong>{{ $pct }}%</strong> is a starting point. Review your answer explanations, use the AI counselor chat, and practice consistently — every top performer started exactly where you are now.
    @endif
  </div>

  <div class="section-title">What to Do Next on RiseStar AI</div>
  <div class="steps">
    <div class="step"><div class="step-e">✨</div><div class="step-t">AI Blueprint</div></div>
    <div class="step"><div class="step-e">🧠</div><div class="step-t">Skill-Gap</div></div>
    <div class="step"><div class="step-e">🎤</div><div class="step-t">Mock Interview</div></div>
    <div class="step"><div class="step-e">📄</div><div class="step-t">AI Resume</div></div>
    <div class="step"><div class="step-e">💬</div><div class="step-t">AI Counselor</div></div>
  </div>

</div>

{{-- FOOTER --}}
<div class="footer">
  <strong>RiseStar AI Platform</strong>
  <span>Confidential — For Educational Use Only</span>
  <span>© {{ date('Y') }} RiseStar AI</span>
</div>

</body>
</html>
