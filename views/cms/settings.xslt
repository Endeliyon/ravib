<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/tablemanager.xslt" />

<xsl:template match="tablemanager/label">
<div class="labels">
<label>Key:</label>
<div class="form-control" disabled="disabled"><xsl:value-of select="key" /></div>
<label>Type:</label>
<div class="form-control" disabled="disabled"><xsl:value-of select="type" /></div>
</div>
</xsl:template>

<xsl:template match="content">
<xsl:apply-templates select="tablemanager" />
<xsl:apply-templates select="tablemanager/label" />
</xsl:template>

</xsl:stylesheet>
