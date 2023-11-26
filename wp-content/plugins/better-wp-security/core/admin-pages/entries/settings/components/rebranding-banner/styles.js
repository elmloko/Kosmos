/**
 * External dependencies
 */
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { Button } from '@wordpress/components';

/**
 * SolidWP dependencies
 */
import { Heading } from '@ithemes/ui';

export const StyledBanner = styled.aside`
	position: relative;
	display: flex;
	flex-direction: column;
	gap: 1rem;
	background: ${ ( { theme } ) => theme.colors.surface.primary } ;
	padding: 1rem 2rem 1rem 1.5rem;
	justify-content: space-between;
	align-items: center;
	overflow: hidden;
	border-radius: 0.25rem;
	@media screen and (min-width: ${ ( { theme } ) => theme.breaks.medium }px) {
		flex-direction: row;
	}
`;

export const StyledTextContainer = styled.section`
	display: flex;
	flex-direction: column;
	gap: 0.625rem;
	align-items: center;
  
	@media screen and (min-width: ${ ( { theme } ) => theme.breaks.medium }px) {
    	align-items: flex-start;
	}
`;

export const StyledBannerHeading = styled( Heading )`
	font-size: 1.5rem;
	text-align: center;
  
	@media screen and (min-width: ${ ( { theme } ) => theme.breaks.medium }px) {
    	text-align: left;
	}
`;

export const StyledBannerButton = styled.a`
	display: inline-flex;
	min-width: max-content;
	padding: 0.75rem 1.75rem;
	justify-content: center;
	align-items: center;
	color: #ffffff;
	font-size: 0.83569rem;
	text-align: center;
	text-decoration: none;
	background: #6817C5;

  	&:hover, &:active, &:focus {
    	background-color: #53129e;
    	color: ${ ( { theme } ) => theme.colors.text.white };
	}
`;

export const StyledStellarSaleDismiss = styled( Button )`
	position: absolute;
	top: 0;
	right: 0;
	&:hover, &:active, &:focus {
    	color: #6817c5;
	}
`;
