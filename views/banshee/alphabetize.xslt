<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="alphabetize">
<ul class="pagination pagination-sm">
<xsl:for-each select="char">
<xsl:choose>
    <xsl:when test="@link=../@char">
		<li class="disabled"><a href="#"><xsl:value-of select="." /></a></li>
	</xsl:when>
	<xsl:otherwise>
		<li><a href="{/output/page/@url}?char={@link}"><xsl:value-of select="." /></a></li>
	</xsl:otherwise>
</xsl:choose>
</xsl:for-each>
</ul>
<div style="clear:both" />
</xsl:template>

</xsl:stylesheet>
