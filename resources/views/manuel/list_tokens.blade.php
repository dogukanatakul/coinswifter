<table border="1" width="100%">
    <thead>
    <tr>
        <th>Icon</th>
        <th>Token</th>
        <th>Symbol</th>
        <th>Network</th>
        <th>Contract</th>
        <th>Comission</th>
    </tr>
    </thead>
    <tbody>
    @foreach($tokens as $token)
        <tr>
            <td align="center"><img width="20" height="20" src="{{ url("/assets/img/coinicon/".$token->sembol.".png") }}"></td>
            <td align="center">{{ $token->isim }}</td>
            <td align="center">{{ $token->sembol }}</td>
            <td align="center">{{ $token->network }}</td>
            <td align="center">{{ $token->contract }}</td>
            <td align="center">{{ floatval($token->transfer_komisyon) }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
