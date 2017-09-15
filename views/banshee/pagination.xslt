<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="pagination">
<ul class="pagination pagination-sm">
<xsl:choose>
	<xsl:when test="@page=0">
		<li class="disabled"><a href="#">&lt;&lt;</a></li>
		<li class="disabled"><a href="#">&lt;</a></li>
	</xsl:when>
	<xsl:otherwise>
		<li><a href="{/output/page/@url}?offset=0">&lt;&lt;</a></li>
		<li><a href="{/output/page/@url}?offset={@page - @step}">&lt;</a></li>
	</xsl:otherwise>
</xsl:choose>

<xsl:for-each select="page">
<xsl:choose>
	<xsl:when test=".=../@page">
		<li class="disabled"><a href="#"><xsl:value-of select=". + 1" /></a></li>
	</xsl:when>
	<xsl:otherwise>
		<li><a href="{/output/page/@url}?offset={.}"><xsl:value-of select=". + 1" /></a></li>
	</xsl:otherwise>
</xsl:choose>
</xsl:for-each>

<xsl:choose>
	<xsl:when test="@page=@max">
		<li class="disabled"><a href="#">&gt;</a></li>
		<li class="disabled"><a href="#">&gt;&gt;</a></li>
	</xsl:when>
	<xsl:otherwise>
		<li><a href="{/output/page/@url}?offset={@page + @step}">&gt;</a></li>
		<li><a href="{/output/page/@url}?offset={@max}">&gt;&gt;</a></li>
	</xsl:otherwise>
</xsl:choose>
</ul>
<div style="clear:both" />
</xsl:template>

</xsl:stylesheet>
