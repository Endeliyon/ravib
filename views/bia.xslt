<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-condensed table-striped table-hover">
<thead>
<tr>
<th>Informatiesysteem</th>
<th class="availability">Beschikbaarheid</th>
<th class="integrity">Integriteit</th>
<th class="confidentiality">Vertrouwelijkheid</th>
<th class="value">Waarde</th>
<th class="owner">Eigenaar</th>
<th class="location">Lokatie</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="item">
<tr class="click" onClick="javascript:document.location='/{/output/page}/{../@case_id}/{@id}'">
<td><xsl:value-of select="item" /></td>
<td><xsl:value-of select="availability" /></td>
<td><xsl:value-of select="integrity" /></td>
<td><xsl:value-of select="confidentiality" /></td>
<td><xsl:value-of select="value" /></td>
<td><xsl:value-of select="owner" /></td>
<td><xsl:value-of select="location" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/{/output/page}/{@case_id}/new" class="btn btn-default">Nieuw informatiesysteem</a>
<a href="/{/output/page}/{@case_id}/export" class="btn btn-default">Exporteer overzicht</a>
<a href="/dreigingen/{@case_id}" class="btn btn-default">Verder naar dreigingen</a>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}/{@case_id}" method="post">
<xsl:if test="item/@id">
<input type="hidden" name="id" value="{item/@id}" />
</xsl:if>

<label for="item">Informatieysteem:</label>
<input type="text" id="item" name="item" value="{item/item}" class="form-control" />
<label for="description">Omschrijving:</label>
<textarea id="description" name="description" class="form-control"><xsl:value-of select="item/description" /></textarea>
<label for="impact">Impact incident t.a.v. B, I of V:</label>
<textarea id="impact" name="impact" class="form-control"><xsl:value-of select="item/impact" /></textarea>
<label for="availability">Beschikbaarheid:</label>
<select id="availability" name="availability" class="form-control">
<option value="0"></option>
<xsl:for-each select="availability/label">
	<option value="{@value}"><xsl:if test="@value=../../item/availability"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
<label for="integrity">Integriteit:</label>
<select id="integrity" name="integrity" class="form-control">
<option value="0"></option>
<xsl:for-each select="integrity/label">
	<option value="{@value}"><xsl:if test="@value=../../item/integrity"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
<label for="confidentiality">Vertrouwelijkheid:</label>
<select id="confidentiality" name="confidentiality" class="form-control">
<option value="0"></option>
<xsl:for-each select="confidentiality/label">
	<option value="{@value}"><xsl:if test="@value=../../item/confidentiality"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
<label for="onwer">Informatiesysteem toegewezen aan een eigenaar:</label>
<div><input type="checkbox" id="owner" name="owner"><xsl:if test="item/owner='yes'"><xsl:attribute name="checked">checked</xsl:attribute></xsl:if></input></div>
<label for="location">Lokatie:</label>
<select id="location" name="location" class="form-control">
<xsl:for-each select="location/label">
	<option value="{@value}"><xsl:if test="@value=../../item/location"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>

<div class="btn-group">
<input type="submit" name="submit_button" value="Informatiesysteem opslaan" class="btn btn-default" />
<a href="/{/output/page}/{@case_id}" class="btn btn-default">Afbreken</a>
<xsl:if test="item/@id">
<input type="submit" name="submit_button" value="Informatiesysteem verwijderen" class="btn btn-default" onClick="javascript:return confirm('VERWIJDEREN: Weet u het zeker?')" />
</xsl:if>
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
<h1>Business Impact Analyse</h1>
<div class="case"><xsl:value-of select="case" /></div>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
<div id="help">
<h3>Beschikbaarheid en Integriteit</h3>
<ul>
<li><b>Normaal:</b> Het informatiesysteem behoort niet bij een vitaal proces.</li>
<li><b>Belangrijk:</b> Het informatiesysteem behoort bij een vitaal proces en een incident op dit vlak zorgt voor kleine problemen en/of schade.</li>
<li><b>Cruciaal:</b> Het informatiesysteem behoort bij een vitaal proces en een incident op dit vlak zorgt voor grote problemen en/of schade.</li>
</ul>
<h3>Vertrouwelijkheid</h3>
<ul>
<li><b>Openbaar:</b> Informatie mag door iedereen ingezien worden.</li>
<li><b>Intern:</b> Informatie mag alleen door eigen medewerkers en eventueel een select aantal zakelijke partners ingezien worden.</li>
<li><b>Vertrouwelijk:</b> Informatie mag door een select aantal eigen mederwerkers ingezien worden. Inbreuk op de vertrouwelijkheid heeft een serieuze impact op de eigen organisatie. Vertrouwelijkheid is waarschijnlijk afgedwongen door (privacy)wetgeving.</li>
<li><b>Geheim:</b> Informatie mag door een select aantal eigen medewerkers ingezien worden. Inbreuk op de vertrouwelijkheid heeft een serieuze impact op de eigen organisatie, maar ook op andere instanties. Vertrouwelijkheid is waarschijnlijk afgedwongen door wetgeving (staatsgeheimen).</li>
</ul>
<h3>Waarde</h3>
<p>De waarde van het informatiesysteem wordt automatisch bepaald op basis van de ingevulde beschikbaarheid, integriteit en vertrouwelijkheid.</p>
<h3>Eigenaar</h3>
<p>Met eigenaar wordt een systeemeigenaar bedoeld. Een systeemeigenaar is verantwoordelijk voor het regelen budget, optuigen van een beheerorganisatie, het maken van SLA afspraken met de ICT-afdeling / leverancier, het opstellen van de autorisatiematrix, zorgen voor het voldoen aan de privacywetgeving (meldplicht datalekken), het opstellen noodplan voor problemen met B, I en/of V, het zorgdragen voor documentatie en het hebben van toekomstvisie.</p>
</div>
</xsl:template>

</xsl:stylesheet>
