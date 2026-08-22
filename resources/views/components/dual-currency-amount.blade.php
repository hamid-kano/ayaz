@props(['syp' => 0, 'usd' => 0, 'allowZeroUsd' => false, 'allowZeroSyp' => false])

<div class="dual-amount">
    @if($syp != 0 || $allowZeroSyp)
        <span class="dual-amount-line">{{ \App\Helpers\TranslationHelper::formatAmount($syp) }} ل.س</span>
    @endif
    @if($usd != 0 || $allowZeroUsd)
        <span class="dual-amount-line">{{ \App\Helpers\TranslationHelper::formatAmount($usd) }} $</span>
    @endif
    @if($syp == 0 && $usd == 0 && !$allowZeroSyp && !$allowZeroUsd)
        <span class="dual-amount-line">0 ل.س</span>
    @endif
</div>
