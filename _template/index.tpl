<h2>Ici la page d'index</h2>
<ul>
{LOOP:testBoucle}
<li>{#value#}</li>
{/LOOP}
</ul>
{:SESSION name="test" context="public"}