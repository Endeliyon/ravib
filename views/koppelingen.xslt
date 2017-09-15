<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />
<xsl:include href="includes/iso_standards.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="links">
<xsl:apply-templates select="iso_standards" />

<ul class="tabs">
<li class="selected" id="threat" onClick="show_threats()">Dreigingen</li>
<li id="measure" onClick="show_measures()">Maatregelen</li>
</ul>

<div class="threats">
<p>Alle dreigingen met de daaraan gekoppelde maatregelen uit de <xsl:value-of select="iso_standards/standard[@selected='yes']" /> standaard.</p>
<xsl:for-each select="threats/*">
	<xsl:choose>
	<xsl:when test="name(.)='category'">
		<div class="item">
		<h3><xsl:value-of select="." /></h3>
		</div>
	</xsl:when>
	<xsl:otherwise>
		<div class="item">
		<div class="head" onClick="javascript:$('.measures_{@id}').slideToggle('normal')">
			<xsl:value-of select="number" />. <xsl:value-of select="threat" />
		</div>
		<div class="links measures_{@id}">
		<p><xsl:value-of select="description" /><br />
		Beschikbaarheid: <xsl:value-of select="availability" />, Integriteit: <xsl:value-of select="integrity" />, Vertrouwelijkheid: <xsl:value-of select="confidentiality" /></p>
		<ul>
		<xsl:for-each select="measure">
		<li><xsl:value-of select="." /></li>
		</xsl:for-each>
		</ul>
		</div>
		</div>
	</xsl:otherwise>
	</xsl:choose>
</xsl:for-each>
</div>

<div class="measures">
<p>Alle maatregelen uit de <xsl:value-of select="iso_standards/standard[@selected='yes']" /> standaard met de daaraan gekoppelde dreigingen.</p>
<xsl:for-each select="measures/measure">
	<div class="item">
	<div class="head" onClick="javascript:$('.threats_{@id}').slideToggle('normal')">
		<xsl:value-of select="number" />&#160;<xsl:value-of select="measure" />
	</div>
	<div class="links threats_{@id}">
	<ul>
	<xsl:for-each select="threat">
	<li><xsl:value-of select="." /></li>
	</xsl:for-each>
	</ul>
	</div>
	</div>
</xsl:for-each>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Koppelingen tussen dreigingen en maatregelen</h1>
<xsl:apply-templates select="links" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
