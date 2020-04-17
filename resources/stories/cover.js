import React from 'react';
import { createBlock, serialize } from '@wordpress/blocks';

export default { title: 'Cover' };

const toInnerBlocks = (children) => {
  return React.Children.map(children, (child) => child.type(child.props));
};

const Heading = (props) => {
  return createBlock('core/heading', props, toInnerBlocks(props.children))
}
const Cover = (props) => {
  return createBlock('core/cover', props, toInnerBlocks(props.children))
}

const BlockSaveContent = (props) => {
  return <div dangerouslySetInnerHTML={{ __html: serialize(toInnerBlocks(props.children)) }} />
};

export const coverSolidColor = () => {
  return (
    <BlockSaveContent>
      <Cover overlayColor="primary">
        <Heading content="With Primary color" />
      </Cover>
      <Cover overlayColor="secondary">
        <Heading content="With Secondary color" />
      </Cover>
    </BlockSaveContent>
  );
};

export const coverBackgroundImage = () => {
  const defaults = {
    backgroundType: 'image',
    url: 'https://cldup.com/Fz-ASbo2s3.jpg',
  };

  return (
    <BlockSaveContent>
      <Cover {...defaults}>
        <Heading content="With defaults" />
      </Cover>
      <Cover {...defaults} dimRatio={ 10 }>
        <Heading content="Custom dim ratio" />
      </Cover>
      <Cover {...defaults} overlayColor="primary">
        <Heading content="Overlay color" />
      </Cover>
      <Cover {...defaults} minHeight={ 200 }>
        <Heading content="Minimum height" />
      </Cover>
      <Cover {...defaults} hasParallax={ true }>
        <Heading content="Parallax" />
      </Cover>
    </BlockSaveContent>
  );
};
