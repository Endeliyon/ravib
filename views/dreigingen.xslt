<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Risk analysis template
//
//-->
<xsl:template match="riskanalysis">
<div class="js_warning">Voor een goede werking van deze website is Javascript nodig. Deze staat momenteel uitgeschakeld. Gegevens worden daardoor niet opgeslagen!</div>
<table class="threats table-condensed table-xs">
<thead>
<tr>
<th class="number">#</th>
<th class="threat">Dreiging</th>
<th class="cia">B</th>
<th class="cia">I</th>
<th class="cia">V</th>
<th class="chance">Kans</th>
<th class="impact">Impact</th>
<th class="handle">Aanpak</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="threats/threat">
<xsl:if test="@category">
<tr class="category">
<td colspan="2"><xsl:value-of select="@category" /></td>
<td>B</td>
<td>I</td>
<td>V</td>
<td>Kans</td>
<td>Impact</td>
<td>Aanpak</td>
</tr>
</xsl:if>
<tr class="threats">
<td><xsl:value-of select="number" /></td>
<td class="threat" onClick="javascript:open_text({@id})"><xsl:value-of select="threat" /></td>
<td><span class="header">B:</span><xsl:value-of select="availability" /></td>
<td><span class="header">I:</span><xsl:value-of select="integrity" /></td>
<td><span class="header">V:</span><xsl:value-of select="confidentiality" /></td>
<td><span class="header">Kans:</span><div><select id="chance_{@id}" onChange="javascript:save_input({../../@case_id}, 'chance_{@id}');">
<xsl:variable name="chance" select="chance" />
<xsl:for-each select="../../chance/value">
	<option value="{@value}"><xsl:if test="@value=$chance"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
	<xsl:value-of select="." /></option>
</xsl:for-each>
</select></div></td>
<td><span class="header">Impact:</span><div><select id="impact_{@id}" onChange="javascript:save_input({../../@case_id}, 'impact_{@id}');">
<xsl:variable name="impact" select="impact" />
<xsl:for-each select="../../impact/value">
	<option value="{@value}"><xsl:if test="@value=$impact"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
	<xsl:value-of select="." /></option>
</xsl:for-each>
</select></div></td>
<td><span class="header">Aanpak:</span><div><select id="handle_{@id}" onChange="javascript:save_input({../../@case_id}, 'handle_{@id}');">
<xsl:variable name="handle" select="handle" />
<xsl:for-each select="../../handle/option">
	<option><xsl:if test=".=$handle"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if>
	<xsl:value-of select="." /></option>
</xsl:for-each>
</select></div></td>
</tr>
<tr class="extra extra_{@id}">
<td colspan="9">
	<div class="row">
	<div class="col-sm-4"><div>Huidige situatie / huidige maatregelen:</div><textarea id="current_{@id}" class="form-control" onBlur="javascript:save_input({../../@case_id}, 'current_{@id}')"><xsl:value-of select="current" /></textarea></div>
	<div class="col-sm-4"><div>Gewenste situatie / te nemen acties:</div><textarea id="action_{@id}" class="form-control" onBlur="javascript:save_input({../../@case_id}, 'action_{@id}')"><xsl:value-of select="action" /></textarea></div>
	<div class="col-sm-4"><div>Argumentatie voor gemaakte keuze:</div><textarea id="argumentation_{@id}" class="form-control" onBlur="javascript:save_input({../../@case_id}, 'argumentation_{@id}')"><xsl:value-of select="argumentation" /></textarea></div>
	</div>
</td>
</tr>
<tr class="extra extra_{@id} description">
<td colspan="9">
	<xsl:value-of select="description" />
</td>
</tr>
<tr class="extra extra_{@id} measures"><td colspan="9">
<xsl:for-each select="measures/item">
	<div><xsl:value-of select="number" />: <xsl:value-of select="name" /></div>
</xsl:for-each>
</td></tr>
<tr class="extra extra_{@id} bia"><td colspan="9">
<div>Relevant voor:</div>
<xsl:for-each select="systems/item">
	<xsl:variable name="bia_id" select="@id" />
	<div class="bia" id="bt_{@id}_{../../@id}"><input type="checkbox" onChange="javascript:save_bia_threat({../../../../@case_id}, {@id}, {../../@id});"><xsl:if test="checked='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input><xsl:value-of select="../../../../bia/item[@id=$bia_id]" /> <img src="/images/level_{level}.png" class="level" /></div>
</xsl:for-each>
</td></tr>
</xsl:for-each>
</tbody>
</table>

<form action="/{/output/page}/{../case/@id}" method="post">
<div class="btn-group">
<input type="submit" name="submit_button" value="Exporteer dreigingen" class="btn btn-default" />
<a href="/iso/{@case_id}" class="btn btn-default">Verder naar maatregelen</a>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<xsl:apply-templates select="breadcrumbs" />
<h1>Dreigingen</h1>
<div class="case"><xsl:value-of select="case" /></div>
<xsl:apply-templates select="riskanalysis" />
<xsl:apply-templates select="result" />

<div id="help">
<p>De kolommen B, I en V staan voor Beschikbaarheid, Integriteit en Vertrouwelijkheid. De waarden p en s staan voor primair en secundair. Een p in de kolom B, betekent dus dat de dreiging primair een bedreiging vormt voor de beschikbaarheid van een informatiesysteem.</p>

<p>Houd bij het kiezen van de kans rekening met de benodigde kennis voor de dreiging, wie de mogelijke aanvaller is, of de organisatie een concreet doelwit is, wat de kans op het maken van een fout of vergissing is, etc.</p>

<p>De waarden in het Aanpak-veld hebben de volgende betekenis:</p>
<ul>
<li>Beheersen: Het nemen van maatregelen om de impact van en de kans op een incident te verkleinen.</li>
<li>Ontwijken: Het nemen van maatregelen om de kans op een incident te verkleinen.</li>
<li>Verweren: Het nemen van maatregelen om de impact van een incident te verkleinen.</li>
<li>Accepteren: Het accepteren van de gevolgen indien een incident zich voordoet.</li>
</ul>

<p>De stoplichtblokjes (<img src="/images/level_3.png" class="level" /> ) achter een informatiesysteem worden opgebouwd door de B, I en V uit de BIA stap te combineren met de B, I en V kolommen uit deze pagina. Een stoplichtblokje wordt voor een informatiesysteem volledig gevuld als, bijvoorbeeld, de beschikbaarheid van dit systeem cruciaal is en een dreiging primair een bedreiging vormt voor de beschikbaarheid.</p>
</div>
</xsl:template>

</xsl:stylesheet>
