<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />
<xsl:include href="../banshee/tablemanager.xslt" />

<xsl:template match="content">
<xsl:apply-templates select="tablemanager" />

<xsl:if test="tablemanager/users">
<h4>Users for this organisation:</h4>
<table class="table table-striped table-condensed">
<thead>
<tr><th>Name</th><th>E-mail address</th></tr>
</thead>
<tbody>
<xsl:for-each select="tablemanager/users/user">
<tr onClick="javascript:location='/cms/user/{@id}'" class="click">
<td><xsl:value-of select="fullname" /></td>
<td><xsl:value-of select="email" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:if>
</xsl:template>

</xsl:stylesheet>
