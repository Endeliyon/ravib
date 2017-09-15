<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<xsl:call-template name="show_messages" />

<form action="/{/output/page}" method="post">
<table class="table table-condensed table-striped">
<thead>
<tr><th>Casus</th><th>ISO standaard</th><th class="compare">Vergelijk?</th></tr>
</thead>
<tbody>
<xsl:for-each select="case">
<tr>
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="iso_standard" /></td>
<td class="compare"><input type="checkbox" name="compare[]" value="{@id}" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<input type="submit" name="submit_button" value="Vergelijk" class="btn btn-default" />
<a href="/casus" class="btn btn-default">Terug</a>
</div>
</form>
</xsl:template>

<!--
//
//  Compare template
//
//-->
<xsl:template match="compare">
<table class="table table-condensed table-striped table-xs compare">
<thead>
<tr><th></th><th></th>
<xsl:for-each select="cases/case">
<th class="case"><div><xsl:value-of select="." /></div></th>
</xsl:for-each>
</tr>
</thead>
<tbody>
<xsl:for-each select="threat">
<tr>
<td class="id"><xsl:value-of select="@id" /></td>
<td class="threat"><xsl:value-of select="threat" /></td>
<xsl:for-each select="cases/case">
<td class="risk risk_{.} accept_{@accept}"><xsl:value-of select="." /></td>
</xsl:for-each>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/{/output/page}" class="btn btn-default">Terug</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Casus vergelijking</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="compare" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
