<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Matrix template
//
//-->
<xsl:template match="matrix">
<p>De door RAVIB.nl gehanteerde risicomatrix is als volgt:</p>
<table class="matrix">
<tr><td></td><td></td><td colspan="{count(row[position()=1]/cell)-1}">Impact</td></tr>
<xsl:for-each select="row">
<tr>
	<xsl:if test="position()=1"><td></td></xsl:if>
	<xsl:if test="position()=2"><td rowspan="{count(../row)-1}" class="chance">Kans</td></xsl:if>
	<xsl:for-each select="cell">
		<td><xsl:if test="@class"><xsl:attribute name="class"><xsl:value-of select="@class" /></xsl:attribute></xsl:if><xsl:value-of select="." /></td>
	</xsl:for-each>
</tr>
</xsl:for-each>
</table>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Risicomatrix</h1>
<xsl:apply-templates select="matrix" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
