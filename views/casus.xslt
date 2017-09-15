<?xml version="1.0" ?>
<!--
//
//  LICENSE
//
//-->
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<form action="/{/output/page}" method="post">
<table class="table table-condensed table-striped">
<thead>
<tr>
<th>Onderwerp</th>
<th>ISO standaard</th>
<th>Datum</th>
<th></th>
<th>Zbh</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="case">
<tr>
<td><xsl:if test="organisation!=''"><xsl:value-of select="organisation" /> :: </xsl:if><xsl:value-of select="name" /></td>
<td><xsl:value-of select="iso_standard" /></td>
<td><xsl:value-of select="date" /></td>
<td><a href="/{start}/{@id}" class="btn btn-xs btn-primary">Start</a> <a href="/{/output/page}/{@id}" class="btn btn-xs btn-default">Bewerk</a></td>
<td><input type="checkbox" name="visible[]" value="{@id}" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group visibility">
<input type="submit" name="submit_button" value="Zet zichtbaarheid" class="btn btn-default" />
<input type="submit" name="submit_button" value="Toon alle casussen" class="btn btn-default" />
</div>
<div class="btn-group">
<a href="/{/output/page}/new" class="btn btn-default">Nieuwe casus</a>
</div>
</form>

<div id="help">
<p>Gebruik de zichtbaarheids-opties (Zbh) om casussen te verbergen. Dit is handig indien u adviseur bent en casussen van andere klanten wilt verbergen.</p>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="case/@id">
<input type="hidden" name="id" value="{case/@id}" />
</xsl:if>

<div class="row">
<div class="col-md-6">
<label for="name">Organisatie:</label>
<input type="text" id="organisation" name="organisation" value="{case/organisation}" class="form-control" />
<label for="name">Naam van de casus (tip: scope in &#233;&#233;n woord):</label>
<input type="text" id="name" name="name" value="{case/name}" class="form-control" />
<label for="iso_standard_id">ISO standaard:</label>
<select id="iso_standard_id" name="iso_standard_id" class="form-control">
<xsl:for-each select="standards/standard">
<option value="{@id}"><xsl:if test="@id=../../case/iso_standard_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
<label for="date">Datum:</label>
<input type="text" id="date" name="date" value="{case/date}" class="form-control datepicker" />
<label for="scope">Scope. De risicoanalyse heeft zich beperkt tot ...</label>
<textarea id="scope" name="scope" class="form-control"><xsl:value-of select="case/scope" /></textarea>
<label for="logo">URL van logo voor in rapportage:</label>
<input type="text" id="logo" name="logo" value="{case/logo}" class="form-control" />
</div>

<div class="col-md-6">
<h3>Invulling van de impact</h3>
<p>Vul hier het verwachte schadebedrag en/of de verwachte imagoschade in.</p>
<xsl:for-each select="impact/value">
<label for="impact_{position()}"><xsl:value-of select="@label" /></label>
<input type="text" id="impact_{position()}" name="impact[]" value="{.}" class="form-control" />
</xsl:for-each>
</div>
</div>

<div class="btn-group">
<input type="submit" name="submit_button" value="Casus opslaan" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Afbreken</a>
<xsl:if test="case/@id">
<input type="submit" name="submit_button" value="Casus verwijderen" class="btn btn-default" onClick="javascript:return confirm('VERWIJDEREN: Weet je het zeker?')" />
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
<h1>Casussen</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
