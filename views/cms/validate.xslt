<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Validate template
//
//-->
<xsl:template match="validate">
<h2>Links to ISO measures</h2>
<table class="table table-condensed table-striped table-xs">
<thead>
<tr>
<th class="id">#</th>
<th class="text">Threat</th>
<th class="links">Links</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="threats/threat">
<tr><xsl:if test="links=0"><xsl:attribute name="class">alert</xsl:attribute></xsl:if>
<td><xsl:value-of select="number" /></td>
<td><xsl:value-of select="threat" /></td>
<td><xsl:value-of select="links" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<h2>Links to threats</h2>
<table class="table table-condensed table-striped table-xs">
<thead>
<tr>
<th class="id">#</th>
<th class="text">ISO measure</th>
<th class="links">Links</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="measures/measure">
<tr><xsl:if test="links=0"><xsl:attribute name="class">alert</xsl:attribute></xsl:if>
<td><xsl:value-of select="number" /></td>
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="links" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/cms" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<div class="standard"><xsl:value-of select="standard" /></div>
<h1><img src="/images/icons/validate.png" class="title_icon" />Validation</h1>
<xsl:apply-templates select="validate" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
