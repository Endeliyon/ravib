<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Form template
//
//-->
<xsl:template match="form">
<form action="/{/output/page}" method="post">
<input type="hidden" name="code" value="{code}" />
<p>U staat op het punt om de taak <u><xsl:value-of select="measure" /></u>, inzake <xsl:value-of select="name" /> en toegekend aan <xsl:value-of select="fullname" />, goed te keuren.</p>
<p class="info"><xsl:value-of select="info" /></p>
<input type="submit" name="submit_button" value="Gereedmelden" class="btn btn-default" onClick="javascript:return confirm('GEREEDMELDEN: Weet u het zeker?')" />
</form>
</xsl:template>

<!--
//
//  Result template
//
//-->
<xsl:template match="result">
<p><xsl:value-of select="."/></p>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Taak gereed</h1>
<xsl:apply-templates select="form" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
