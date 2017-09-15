<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="graph">
<div class="graph panel panel-default" style="max-width:{@width}px">
	<xsl:if test="title"><div class="panel-heading"><xsl:value-of select="title" /></div></xsl:if>
	<div class="panel-body">
		<div class="info">
			<span id="text_{@id}" class="text"></span>
			<span id="value_{@id}" class="value"></span>
		</div>
		<div class="maxy"><xsl:value-of select="@max_y" /></div>
		<div class="bars">
			<table style="height:{@height}px"><tr>
				<xsl:for-each select="bar">
				<td onMouseOver="javascript:show_info({../@id}, '{@text}', '{@value}')" onMouseOut="javascript:show_info({../@id}, '', '')">
					<xsl:if test="@link">	
						<xsl:attribute name="onClick">javascript:document.location='<xsl:value-of select="@link" />'</xsl:attribute>
						<xsl:attribute name="class">clickable</xsl:attribute>
					</xsl:if>
					<div style="height:{.}px"></div>
				</td>
				</xsl:for-each>
			</tr></table>
		</div>
	</div>
</div>
</xsl:template>

</xsl:stylesheet>
