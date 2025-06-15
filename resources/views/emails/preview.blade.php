<div style="background:#111;padding:20px;border-radius:8px;color:#fff;">
    <p>{!! nl2br(str_replace(
        ['{{ nome }}', '{{ plano }}', '{{ vencimento }}'],
        ['<b>' . $nome . '</b>', '<b>' . $plano . '</b>', '<b>' . $vencimento . '</b>'],
        $mensagem ?? ''
    )) !!}</p>
</div>
