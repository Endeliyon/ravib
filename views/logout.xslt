<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Uitgelogd</h1>
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
